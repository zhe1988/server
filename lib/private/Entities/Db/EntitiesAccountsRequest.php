<?php
declare(strict_types=1);


/**
 * Nextcloud - Social Support
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2018, Maxence Lange <maxence@artificial-owl.com>
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


namespace OC\Entities\Db;


use DateTime;
use OC\Entities\Classes\IEntitiesAccounts\LocalUser;
use OC\Entities\Exceptions\EntityAccountNotFoundException;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\Entities\Model\IEntityAccount;

/**
 * Class EntitiesRequest
 *
 * @package OC\Entities\Db
 */
class EntitiesAccountsRequest extends EntitiesAccountsRequestBuilder {


	public function create(IEntityAccount $account) {
		$now = new DateTime('now');

		$qb = $this->getEntitiesAccountsInsertSql();
		$qb->setValue('id', $qb->createNamedParameter($account->getId()))
		   ->setValue('type', $qb->createNamedParameter($account->getType()))
		   ->setValue('account', $qb->createNamedParameter($account->getAccount()))
		   ->setValue('creation', $qb->createNamedParameter($now, IQueryBuilder::PARAM_DATE));

		$qb->execute();

		$account->setCreation($now->getTimestamp());
	}


	/**
	 * @param string $userId
	 *
	 * @return IEntityAccount
	 * @throws EntityAccountNotFoundException
	 */
	public function getFromLocalUserId(string $userId) {
		$qb = $this->getEntitiesAccountsSelectSql();

		$qb->limitToType(LocalUser::TYPE);
		$qb->limitToAccount($userId);

		return $this->getItemFromRequest($qb);
	}


	/**
	 * @param IQueryBuilder $qb
	 *
	 * @return IEntityAccount
	 * @throws EntityAccountNotFoundException
	 */
	public function getItemFromRequest(IQueryBuilder $qb): IEntityAccount {
		$cursor = $qb->execute();
		$data = $cursor->fetch();
		$cursor->closeCursor();

		if ($data === false) {
			throw new EntityAccountNotFoundException();
		}

		return $this->parseEntitiesAccountsSelectSql($data);
	}


	/**
	 * @param IQueryBuilder $qb
	 *
	 * @return IEntityAccount[]
	 */
	public function getListFromRequest(IQueryBuilder $qb): array {
		$accounts = [];
		$cursor = $qb->execute();
		while ($data = $cursor->fetch()) {
			$accounts[] = $this->parseEntitiesAccountsSelectSql($data);
		}
		$cursor->closeCursor();

		return $accounts;
	}


	/**
	 *
	 */
	public function clearAll(): void {
		$qb = $this->getEntitiesAccountsDeleteSql();

		$qb->execute();
	}


}

