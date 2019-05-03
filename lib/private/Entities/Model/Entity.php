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


namespace OC\Entities\Model;


use daita\NcSmallPhpTools\Traits\TArrayTools;
use daita\NcSmallPhpTools\Traits\TStringTools;
use JsonSerializable;
use OCP\Entities\Model\IEntity;
use OCP\Entities\Model\IEntityMember;


/**
 * Class Entity
 *
 * @package OC\Entities\Model
 */
class Entity implements IEntity, JsonSerializable {


	use TArrayTools;
	use TStringTools;


	/** @var string */
	private $id = '';

	/** @var string */
	private $type = '';

	/** @var string */
	private $ownerId = '';

	/** @var int */
	private $visibility = 0;

	/** @var int */
	private $access = 0;

	/** @var string */
	private $name = '';

	/** @var int */
	private $creation = 0;


	/**
	 * Entity constructor.
	 *
	 * @param string $id
	 */
	public function __construct(string $id = '') {
		$this->id = $id;


		if ($this->id === '') {
			$this->id = $this->uuid(11);
		}
	}


	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @param string $id
	 *
	 * @return IEntity
	 */
	public function setId(string $id): IEntity {
		$this->id = $id;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @param string $type
	 *
	 * @return IEntity
	 */
	public function setType(string $type): IEntity {
		$this->type = $type;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getOwnerId(): string {
		return $this->ownerId;
	}

	/**
	 * @param string $ownerId
	 *
	 * @return IEntity
	 */
	public function setOwnerId(string $ownerId): IEntity {
		$this->ownerId = $ownerId;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getVisibility(): int {
		return $this->visibility;
	}

	/**
	 * @param int $visibility
	 *
	 * @return IEntity
	 */
	public function setVisibility(int $visibility): IEntity {
		$this->visibility = $visibility;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getAccess(): int {
		return $this->access;
	}

	/**
	 * @param int $access
	 *
	 * @return IEntity
	 */
	public function setAccess(int $access): IEntity {
		$this->access = $access;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return IEntity
	 */
	public function setName(string $name): IEntity {
		$this->name = $name;

		return $this;
	}


	/**
	 * @return IEntityMember[]
	 */
	public function getMembers(): array {
		return [];
	}

	/**
	 * @param IEntityMember[] $members
	 *
	 * @return IEntity
	 */
	public function setMembers(array $members): IEntity {
		return $this;
	}

	/**
	 * @param IEntityMember[] $members
	 *
	 * @return IEntity
	 */
	public function addMembers(array $members): IEntity {
		return $this;
	}

	/**
	 * @param IEntityMember $member
	 *
	 * @return IEntity
	 */
	public function addMember(IEntityMember $member): IEntity {
		return $this;
	}


	/**
	 * @return int
	 */
	public function getCreation(): int {
		return $this->creation;
	}

	/**
	 * @param int $creation
	 *
	 * @return IEntity
	 */
	public function setCreation(int $creation): IEntity {
		$this->creation = $creation;

		return $this;
	}


	/**
	 * @param array $data
	 *
	 * @return IEntity
	 */
	public function importFromDatabase(array $data): IEntity {
		$this->setId($this->get('id', $data, ''));
		$this->setType($this->get('type', $data, ''));
		$this->setOwnerId($this->get('owner_id', $data, ''));
		$this->setVisibility($this->getInt('visibility', $data, 0));
		$this->setAccess($this->getInt('access', $data, 0));
		$this->setName($this->get('name', $data, ''));
		$this->setCreation($this->getInt('creation', $data, 0));

		return $this;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'id'         => $this->getId(),
			'type'       => $this->getType(),
			'owner_id'   => $this->getOwnerId(),
			'visibility' => $this->getVisibility(),
			'access'     => $this->getAccess(),
			'name'       => $this->getName(),
			'creation'   => $this->getCreation()
		];
	}

}

