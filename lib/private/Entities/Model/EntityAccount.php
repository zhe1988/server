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
use OCP\Entities\Model\IEntityAccount;


/**
 * Class EntityAccount
 *
 * @package OC\Entities\Model
 */
class EntityAccount implements IEntityAccount, JsonSerializable {


	use TArrayTools;
	use TStringTools;


	/** @var string */
	private $id = '';

	/** @var string */
	private $type = '';

	/** @var string */
	private $account = '';

	/** @var int */
	private $creation = 0;


	/**
	 * EntityAccount constructor.
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
	 * @return IEntityAccount
	 */
	public function setId(string $id): IEntityAccount {
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
	 * @return IEntityAccount
	 */
	public function setType(string $type): IEntityAccount {
		$this->type = $type;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getAccount(): string {
		return $this->account;
	}

	/**
	 * @param string $account
	 *
	 * @return IEntityAccount
	 */
	public function setAccount(string $account): IEntityAccount {
		$this->account = $account;

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
	 * @return IEntityAccount
	 */
	public function setCreation(int $creation): IEntityAccount {
		$this->creation = $creation;

		return $this;
	}


	/**
	 * @param array $data
	 *
	 * @return IEntityAccount
	 */
	public function importFromDatabase(array $data): IEntityAccount {
		$this->setId($this->get('id', $data, ''));
		$this->setType($this->get('type', $data, ''));
		$this->setAccount($this->get('account', $data, ''));
		$this->setCreation($this->getInt('creation', $data, 0));

		return $this;
	}


	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'id'       => $this->getId(),
			'type'     => $this->getType(),
			'account'  => $this->getAccount(),
			'creation' => $this->getCreation()
		];
	}

}

