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
use OC\Entities\Model\EntityType;
use OCP\Entities\Model\IEntityType;


/**
 * Class EntitiesTypesRequestBuilder
 *
 * @package OC\Entities\Db
 */
class EntitiesTypesRequestBuilder extends CoreRequestBuilder {


	use TArrayTools;


	/**
	 * Base of the Sql Insert request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesTypesInsertSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->insert(self::TABLE_ENTITIES_TYPES);

		return $qb;
	}


	/**
	 * Base of the Sql Update request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesTypesUpdateSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->update(self::TABLE_ENTITIES_TYPES);

		return $qb;
	}


	/**
	 * Base of the Sql Select request for Entities Accounts
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesTypesSelectSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();

		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$qb->select('et.id', 'et.type', 'et.interface', 'et.class')
		   ->from(self::TABLE_ENTITIES_TYPES, 'et');

		$qb->setDefaultSelectAlias('et');

		return $qb;
	}


	/**
	 * Base of the Sql Delete request
	 *
	 * @return EntitiesQueryBuilder
	 */
	protected function getEntitiesTypesDeleteSql(): EntitiesQueryBuilder {
		$qb = $this->getQueryBuilder();
		$qb->delete(self::TABLE_ENTITIES_TYPES);

		return $qb;
	}


	/**
	 * @param array $data
	 *
	 * @return IEntityType
	 */
	protected function parseEntitiesTypesSelectSql(array $data): IEntityType {
		$entityType = new EntityType();
		$entityType->importFromDatabase($data);

		return $entityType;
	}

}

