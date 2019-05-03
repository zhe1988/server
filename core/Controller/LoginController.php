<?php
/**
 * @copyright Copyright (c) 2017, Sandro Lutz <sandro.lutz@temparus.ch>
 * @copyright Copyright (c) 2016 Joas Schilling <coding@schilljs.com>
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Christoph Wurst <christoph@owncloud.com>
 * @author Joas Schilling <coding@schilljs.com>
 * @author Julius Härtl <jus@bitgrid.net>
 * @author justin-sleep <justin@quarterfull.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Sandro Lutz <sandro.lutz@temparus.ch>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Ujjwal Bhardwaj <ujjwalb1996@gmail.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Core\Controller;

use OC\Authentication\Login\Chain;
use OC\Authentication\Login\LoginData;
use OC\Authentication\TwoFactorAuth\Manager;
use OC\Security\Bruteforce\Throttler;
use OC\User\Session;
use OC_App;
use OC_Util;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Defaults;
use OCP\IConfig;
use OCP\ILogger;
use OCP\IRequest;
use OCP\ISession;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\Util;

class LoginController extends Controller {

	const LOGIN_MSG_INVALIDPASSWORD = 'invalidpassword';
	const LOGIN_MSG_USERDISABLED = 'userdisabled';

	/** @var IUserManager */
	private $userManager;
	/** @var IConfig */
	private $config;
	/** @var ISession */
	private $session;
	/** @var IUserSession|Session */
	private $userSession;
	/** @var IURLGenerator */
	private $urlGenerator;
	/** @var ILogger */
	private $logger;
	/** @var Defaults */
	private $defaults;
	/** @var Throttler */
	private $throttler;
	/** @var Chain */
	private $loginChain;

	public function __construct(?string $appName,
								IRequest $request,
								IUserManager $userManager,
								IConfig $config,
								ISession $session,
								IUserSession $userSession,
								IURLGenerator $urlGenerator,
								ILogger $logger,
								Defaults $defaults,
								Throttler $throttler,
								Chain $loginChain) {
		parent::__construct($appName, $request);
		$this->userManager = $userManager;
		$this->config = $config;
		$this->session = $session;
		$this->userSession = $userSession;
		$this->urlGenerator = $urlGenerator;
		$this->logger = $logger;
		$this->defaults = $defaults;
		$this->throttler = $throttler;
		$this->loginChain = $loginChain;
	}

	/**
	 * @NoAdminRequired
	 * @UseSession
	 *
	 * @return RedirectResponse
	 */
	public function logout() {
		$loginToken = $this->request->getCookie('nc_token');
		if (!is_null($loginToken)) {
			$this->config->deleteUserValue($this->userSession->getUser()->getUID(), 'login_token', $loginToken);
		}
		$this->userSession->logout();

		$response = new RedirectResponse($this->urlGenerator->linkToRouteAbsolute(
			'core.login.showLoginForm',
			['clear' => true] // this param the the code in login.js may be removed when the "Clear-Site-Data" is working in the browsers
		));

		$this->session->set('clearingExecutionContexts', '1');
		$this->session->close();
		$response->addHeader('Clear-Site-Data', '"cache", "storage", "executionContexts"');
		return $response;
	}

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 * @UseSession
	 *
	 * @param string $user
	 * @param string $redirect_url
	 *
	 * @return TemplateResponse|RedirectResponse
	 */
	public function showLoginForm(string $user = null, string $redirect_url = null): Http\Response {
		if ($this->userSession->isLoggedIn()) {
			return new RedirectResponse(OC_Util::getDefaultPageUrl());
		}

		$parameters = array();
		$loginMessages = $this->session->get('loginMessages');
		$errors = [];
		$messages = [];
		if (is_array($loginMessages)) {
			list($errors, $messages) = $loginMessages;
		}
		$this->session->remove('loginMessages');
		foreach ($errors as $value) {
			$parameters[$value] = true;
		}

		$parameters['messages'] = $messages;
		if ($user !== null && $user !== '') {
			$parameters['loginName'] = $user;
			$parameters['user_autofocus'] = false;
		} else {
			$parameters['loginName'] = '';
			$parameters['user_autofocus'] = true;
		}

		$autocomplete = $this->config->getSystemValue('login_form_autocomplete', true);
		if ($autocomplete){
			$parameters['login_form_autocomplete'] = 'on';
		} else {
			$parameters['login_form_autocomplete'] = 'off';
		}

		if (!empty($redirect_url)) {
			$parameters['redirect_url'] = $redirect_url;
		}

		$parameters = $this->setPasswordResetParameters($user, $parameters);
		$parameters['alt_login'] = OC_App::getAlternativeLogIns();

		if ($user !== null && $user !== '') {
			$parameters['loginName'] = $user;
			$parameters['user_autofocus'] = false;
		} else {
			$parameters['loginName'] = '';
			$parameters['user_autofocus'] = true;
		}

		$parameters['throttle_delay'] = $this->throttler->getDelay($this->request->getRemoteAddress());

		// OpenGraph Support: http://ogp.me/
		Util::addHeader('meta', ['property' => 'og:title', 'content' => Util::sanitizeHTML($this->defaults->getName())]);
		Util::addHeader('meta', ['property' => 'og:description', 'content' => Util::sanitizeHTML($this->defaults->getSlogan())]);
		Util::addHeader('meta', ['property' => 'og:site_name', 'content' => Util::sanitizeHTML($this->defaults->getName())]);
		Util::addHeader('meta', ['property' => 'og:url', 'content' => $this->urlGenerator->getAbsoluteURL('/')]);
		Util::addHeader('meta', ['property' => 'og:type', 'content' => 'website']);
		Util::addHeader('meta', ['property' => 'og:image', 'content' => $this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath('core', 'favicon-touch.png'))]);

		return new TemplateResponse(
			$this->appName, 'login', $parameters, 'guest'
		);
	}

	/**
	 * Sets the password reset params.
	 *
	 * Users may not change their passwords if:
	 * - The account is disabled
	 * - The backend doesn't support password resets
	 * - The password reset function is disabled
	 *
	 * @param string $user
	 * @param array $parameters
	 * @return array
	 */
	private function setPasswordResetParameters(?string $user,
												array $parameters): array {
		if ($user !== null && $user !== '') {
			$userObj = $this->userManager->get($user);
		} else {
			$userObj = null;
		}

		$parameters['resetPasswordLink'] = $this->config
			->getSystemValue('lost_password_link', '');

		if ($parameters['resetPasswordLink'] === 'disabled') {
			$parameters['canResetPassword'] = false;
		} else if (!$parameters['resetPasswordLink'] && $userObj !== null) {
			$parameters['canResetPassword'] = $userObj->canChangePassword();
		} else if ($userObj !== null && $userObj->isEnabled() === false) {
			$parameters['canResetPassword'] = false;
		} else {
			$parameters['canResetPassword'] = true;
		}

		return $parameters;
	}

	/**
	 * @param string $redirectUrl
	 * @return RedirectResponse
	 */
	private function generateRedirect($redirectUrl) {
		if (!is_null($redirectUrl) && $this->userSession->isLoggedIn()) {
			$location = $this->urlGenerator->getAbsoluteURL(urldecode($redirectUrl));
			// Deny the redirect if the URL contains a @
			// This prevents unvalidated redirects like ?redirect_url=:user@domain.com
			if (strpos($location, '@') === false) {
				return new RedirectResponse($location);
			}
		}
		return new RedirectResponse(OC_Util::getDefaultPageUrl());
	}

	/**
	 * @PublicPage
	 * @UseSession
	 * @NoCSRFRequired
	 * @BruteForceProtection(action=login)
	 *
	 * @param string $user
	 * @param string $password
	 * @param string $redirect_url
	 * @param string $timezone
	 * @param string $timezone_offset
	 * @return RedirectResponse
	 */
	public function tryLogin($user, $password, $redirect_url, $timezone = '', $timezone_offset = '') {
		// If the user is already logged in and the CSRF check does not pass then
		// simply redirect the user to the correct page as required. This is the
		// case when an user has already logged-in, in another tab.
		if(!$this->request->passesCSRFCheck()) {
			return $this->generateRedirect($redirect_url);
		}

		$result = $this->loginChain->process(new LoginData(
			$this->request,
			$user,
			$password,
			$redirect_url,
			$timezone,
			$timezone_offset
		));
		if (!$result->isSuccess()) {
			if ($result->getRedirectUrl() !== null) {
				return new RedirectResponse($result->getRedirectUrl());
			}

			return $this->generateRedirect($redirect_url);
		}

		return $this->createLoginFailedResponse(
			$loginData->getUsername(),
			$user,
			$redirect_url,
			$result->getErrorMessage()
		);
	}

	/**
	 * Creates a login failed response.
	 *
	 * @param string $user
	 * @param string $originalUser
	 * @param string $redirect_url
	 * @param string $loginMessage
	 * @return RedirectResponse
	 */
	private function createLoginFailedResponse(
		$user, $originalUser, $redirect_url, string $loginMessage) {
		// Read current user and append if possible we need to
		// return the unmodified user otherwise we will leak the login name
		$args = !is_null($user) ? ['user' => $originalUser] : [];
		if (!is_null($redirect_url)) {
			$args['redirect_url'] = $redirect_url;
		}
		$response = new RedirectResponse(
			$this->urlGenerator->linkToRoute('core.login.showLoginForm', $args)
		);
		$response->throttle(['user' => substr($user, 0, 64)]);
		$this->session->set('loginMessages', [
			[$loginMessage], []
		]);
		return $response;
	}

	/**
	 * @NoAdminRequired
	 * @UseSession
	 * @BruteForceProtection(action=sudo)
	 *
	 * @license GNU AGPL version 3 or any later version
	 *
	 * @param string $password
	 * @return DataResponse
	 */
	public function confirmPassword($password) {
		$loginName = $this->userSession->getLoginName();
		$loginResult = $this->userManager->checkPassword($loginName, $password);
		if ($loginResult === false) {
			$response = new DataResponse([], Http::STATUS_FORBIDDEN);
			$response->throttle();
			return $response;
		}

		$confirmTimestamp = time();
		$this->session->set('last-password-confirm', $confirmTimestamp);
		return new DataResponse(['lastLogin' => $confirmTimestamp], Http::STATUS_OK);
	}
}
