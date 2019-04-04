<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OC\Core\Controller;

use OC\Authentication\Exceptions\InvalidTokenException;
use OC\Authentication\Exceptions\WipeTokenException;
use OC\Authentication\Token\IProvider;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class WipeController extends Controller {

	/** @var IProvider */
	private $tokenProvider;

	public function __construct(string $appName,
								IRequest $request,
								IProvider $tokenProvider) {
		parent::__construct($appName, $request);

		$this->tokenProvider = $tokenProvider;
	}

	/**
	 * TODO: brute force protection
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 *
	 * @param string $token
	 * @return JSONResponse
	 */
	public function checkWipe(string $token): JSONResponse {
		try {
			$this->tokenProvider->getToken($token);
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		} catch (WipeTokenException $e) {
		} catch (InvalidTokenException $e) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
			//TODO throttle
		}

		return new JSONResponse([
			'wipe' => true
		]);

		//TODO: notification+activity that device retrieved the wipe
	}


	/**
	 * TODO: brute force protection
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 *
	 * @param string $token
	 * @return JSONResponse
	 */
	public function wipeDone(string $token): JSONResponse {
		//TODO: notification that device has ben wiped

		try {
			$this->tokenProvider->getToken($token);
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
		} catch (WipeTokenException $e) {
		} catch (InvalidTokenException $e) {
			return new JSONResponse([], Http::STATUS_NOT_FOUND);
			//TODO throttle
		}

		$this->tokenProvider->invalidateToken($token);

		return new JSONResponse([]);
	}
}
