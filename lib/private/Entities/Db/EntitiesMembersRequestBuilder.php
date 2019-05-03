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


use daita\NcSmallPhpTools\Traits\TArrayTools;
use OC\Entities\Model\EntityMember;
use OCP\DB\QueryBuilder\IQueryBuilder;


/**
 * Class EntitiesMembersRequestBuilder
 *
 * @package OC\Entities\Db
 */
class EntitiesMembersRequestBuilder extends CoreRequestBuilder {


	use TArrayTools;


	/**
	 * Base of the Sql Insert request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesMembersInsertSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_ENTITIES_MEMBERS);

		return $qb;
	}


	/**
	 * Base of the Sql Update request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesMembersUpdateSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_ENTITIES_MEMBERS);

		return $qb;
	}


	/**
	 * Base of the Sql Select request for Entities Accounts
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesMembersSelectSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$qb->select(
			'em.id', 'em.entity_id', 'em.account_id', 'em.slave_entity_id', 'em.status', 'em.level',
			'em.creation'
		)
		   ->from(self::TABLE_ENTITIES_MEMBERS, 'em');

		$qb->setDefaultSelectAlias('em');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesMembersDeleteSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_ENTITIES_MEMBERS);

		return $qb;
	}


	/**
	 * @param array $data
	 *
	 * @return EntityMember
	 */
	protected function parseEntitiesMembersSelectSql(array $data): EntityMember {
		$entityMember = new EntityMember();
		$entityMember->importFromDatabase($data);

		return $entityMember;
	}

}

