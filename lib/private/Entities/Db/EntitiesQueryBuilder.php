<?php
declare(strict_types=1);


/**
 * Entities - Entity & Groups of Entities
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2019, Maxence Lange <maxence@artificial-owl.com>
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


use daita\NcSmallPhpTools\Db\ExtendedQueryBuilder;
use OC\SystemConfig;
use OCP\Entities\IEntitiesQueryBuilder;
use OCP\IDBConnection;
use OCP\ILogger;


class EntitiesQueryBuilder extends ExtendedQueryBuilder implements IEntitiesQueryBuilder {


	/**
	 * CoreRequestBuilder constructor.
	 *
	 * @param IDBConnection $connection
	 * @param SystemConfig $config
	 * @param ILogger $logger
	 */
	public function __construct(IDBConnection $connection, SystemConfig $config, ILogger $logger) {
		parent::__construct($connection, $config, $logger);
	}


	/**
	 * Limit the request to the Interface
	 *
	 * @param string $interface
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToInterface(string $interface): IEntitiesQueryBuilder {
		$this->limitToDBField('interface', $interface, false);

		return $this;
	}


	/**
	 * Limit the request to the Type
	 *
	 * @param string $type
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToType(string $type): IEntitiesQueryBuilder {
		$this->limitToDBField('type', $type, false);

		return $this;
	}


	/**
	 * Limit the request to the OwnerId
	 *
	 * @param string $ownerId
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToOwnerId(string $ownerId): IEntitiesQueryBuilder {
		$this->limitToDBField('owner_id', $ownerId, false);

		return $this;
	}


	/**
	 * Limit the request to the Name
	 *
	 * @param string $name
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToName(string $name): IEntitiesQueryBuilder {
		$this->limitToDBField('name', $name, false);

		return $this;
	}


	/**
	 * @param string $account
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToAccount(string $account): IEntitiesQueryBuilder {
		$this->limitToDBField('account', $account, false);

		return $this;
	}


	/**
	 * @param string $accountId
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToAccountId(string $accountId): IEntitiesQueryBuilder {
		$this->limitToDBField('account_id', $accountId, false);

		return $this;
	}


	/**
	 * @param string $entityId
	 *
	 * @return IEntitiesQueryBuilder
	 */
	public function limitToEntityId(string $entityId): IEntitiesQueryBuilder {
		$this->limitToDBField('entity_id', $entityId, false);

		return $this;
	}

}
