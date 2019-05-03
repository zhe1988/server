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
use OC\Entities\Exceptions\EntityAccountNotFoundException;
use OC\Entities\Exceptions\EntityMemberNotFoundException;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\Entities\Model\IEntityMember;

/**
 * Class EntitiesMembersRequest
 *
 * @package OC\Entities\Db
 */
class EntitiesMembersRequest extends EntitiesMembersRequestBuilder {


	public function create(IEntityMember $member) {
		$now = new DateTime('now');

		$qb = $this->getEntitiesMembersInsertSql();
		$qb->setValue('id', $qb->createNamedParameter($member->getId()))
		   ->setValue('entity_id', $qb->createNamedParameter($member->getEntityId()))
		   ->setValue('account_id', $qb->createNamedParameter($member->getAccountId()))
		   ->setValue('status', $qb->createNamedParameter($member->getStatus()))
		   ->setValue('level', $qb->createNamedParameter($member->getLevel()))
		   ->setValue('creation', $qb->createNamedParameter($now, IQueryBuilder::PARAM_DATE));

		$qb->execute();

		$member->setCreation($now->getTimestamp());
	}


	/**
	 * @param string $accountId
	 * @param string $entityId
	 *
	 * @return IEntityMember
	 * @throws EntityMemberNotFoundException
	 */
	public function getMemberStatus(string $accountId, string $entityId): IEntityMember {
		$qb = $this->getEntitiesMembersSelectSql();
		$qb->limitToEntityId($entityId);
		$qb->limitToAccountId($accountId);

		return $this->getItemFromRequest($qb);
	}


	/**
	 * @param IQueryBuilder $qb
	 *
	 * @return IEntityMember
	 * @throws EntityMemberNotFoundException
	 */
	public function getItemFromRequest(IQueryBuilder $qb): IEntityMember {
		$cursor = $qb->execute();
		$data = $cursor->fetch();
		$cursor->closeCursor();

		if ($data === false) {
			throw new EntityMemberNotFoundException();
		}

		return $this->parseEntitiesMembersSelectSql($data);
	}


	/**
	 * @param IQueryBuilder $qb
	 *
	 * @return IEntityMember[]
	 */
	public function getListFromRequest(IQueryBuilder $qb): array {
		$members = [];
		$cursor = $qb->execute();
		while ($data = $cursor->fetch()) {
			$members[] = $this->parseEntitiesMembersSelectSql($data);
		}
		$cursor->closeCursor();

		return $members;
	}


	/**
	 *
	 */
	public function clearAll(): void {
		$qb = $this->getEntitiesMembersDeleteSql();

		$qb->execute();
	}

}

