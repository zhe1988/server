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


namespace OCP\Entities;


use OC\Entities\Exceptions\EntityAccountAlreadyExistsException;
use OC\Entities\Exceptions\EntityAccountCreationException;
use OC\Entities\Exceptions\EntityAccountNotFoundException;
use OC\Entities\Exceptions\EntityAlreadyExistsException;
use OC\Entities\Exceptions\EntityCreationException;
use OC\Entities\Exceptions\EntityMemberAlreadyExistsException;
use OC\Entities\Exceptions\EntityNotFoundException;
use OC\Entities\Exceptions\EntityTypeNotFoundException;
use OCP\Entities\Model\IEntity;
use OCP\Entities\Model\IEntityAccount;
use OCP\Entities\Model\IEntityMember;


/**
 * Interface IEntitiesManager
 *
 * @since 17.0.0
 *
 * @package OCP\Entities
 */
interface IEntitiesManager {


	/**
	 * @param IEntity $entity
	 *
	 * @throws EntityCreationException
	 * @throws EntityAlreadyExistsException
	 */
	public function saveEntity(IEntity $entity): void;


	/**
	 * @param IEntity $entity
	 *
	 * @return IEntity
	 * @throws EntityNotFoundException
	 * @throws EntityTypeNotFoundException
	 */
	public function searchDuplicateEntity(IEntity $entity): IEntity;


	/**
	 * @param IEntityAccount $account
	 *
	 * @throws EntityAccountCreationException
	 * @throws EntityAccountAlreadyExistsException
	 */
	public function saveAccount(IEntityAccount $account): void;

	/**
	 * @param IEntityMember $member
	 *
	 * @throws EntityMemberAlreadyExistsException
	 */
	public function saveMember(IEntityMember $member): void;

	/**
	 * @param string $entityId
	 *
	 * @return IEntity
	 * @throws EntityNotFoundException
	 */
	public function getEntity(string $entityId): IEntity;

	/**
	 * @param string $userId
	 *
	 * @return IEntityAccount
	 * @throws EntityAccountNotFoundException
	 */
	public function getLocalAccount(string $userId): IEntityAccount;

}

