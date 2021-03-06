<?php

namespace gun\form\forms;

use pocketmine\item\Item;

use gun\form\FormManager;

use gun\provider\ProviderManager;
use gun\provider\SubWeaponShop;
use gun\provider\AccountProvider;

use gun\weapons\WeaponManager;
use gun\weapons\HandGun;

class SubShopForm extends Form
{

	private $weaponType = "";
	private $weaponId = "";

	public function send(int $id)
	{
		$cache = [];
		switch($id)
		{
			case 1://メイン画面
				$buttons = [
						[
							"text" => "§lハンドガン -HandGun-§r§8\n片手で射撃するためにデザインされた銃"
						]
						];
				$cache = [2];
				if($this->player->isOp())
				{
					$buttons[] = ["text" => "§l§e★§8商品の編集 -Edit goods-§r§8\n商品を編集します"];
					$cache[] = 11;
					$buttons[] = ["text" => "§l§e★§c商品の削除 -Edit goods-§r§8\n商品を削除します"];
					$cache[] = 21;
				}
				$data = [
					'type'    => "form",
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => "購入したい武器種を選択してください",
					'buttons' => $buttons
				];
				break;

			case 2://購入武器選択画面
				$buttons = [];
				$provider = SubWeaponShop::get();
				switch($this->lastData)
				{
					case 0:
						$type = HandGun::WEAPON_ID;
						break;
					default:
						$this->close();
						return true;
				}
				$this->weaponType = $type;
				$weaponData = WeaponManager::getAllData($type);
				foreach ($provider->getItems($type) as $key => $value) {
					$buttons[] = [
									"text" => "§l" . $weaponData[$key]["Item_Information"]["Item_Name"] . " §7｜§e " . $value . "P§r§8\n" . $weaponData[$key]["Item_Information"]["Item_Lore"]
								];
					$cache[] = 3;
				}
				$data = [
					'type'    => "form",
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => "購入したい武器を選択してください",
					'buttons' => $buttons
				];
				break;

			case 3://武器購入確認画面(雑いので改善したい)
				$weaponId = array_keys(SubWeaponShop::get()->getItems($this->weaponType))[$this->lastData];
				$this->weaponId = $weaponId;
				$provider = SubWeaponShop::get();
				$price = $provider->getPrice($this->weaponType, $weaponId);
				$content = "この武器を§e". $price ."P§fで購入しますか?\n▼詳細";
				$content .= "\n§a武器名 : " . WeaponManager::getData($this->weaponType, $weaponId)["Item_Information"]["Item_Name"];
				foreach (WeaponManager::getObject($this->weaponType)::ITEM_LORE as $datakey => $data) {
					foreach ($data as $key => $value) {
						$content .= "\n§a{$value} : §f" . WeaponManager::getData($this->weaponType, $weaponId)[$datakey][$key];
					}
				}
				$this->sendModal("§lSubWeaponShop(サブ武器屋)", $content, "購入", "戻る", 4, 1);
				return true;

			case 4:
				$result = "所持金が足りません";
				if(AccountProvider::get()->getPoint($this->player) >= SubWeaponShop::get()->getPrice($this->weaponType, $this->weaponId))
				{
					$result = "購入が完了しました";
					AccountProvider::get()->setSubWeaponData($this->player, 0, $this->weaponType, $this->weaponId);
					AccountProvider::get()->subtractPoint($this->player, SubWeaponShop::get()->getPrice($this->weaponType, $this->weaponId));
				}
				$this->sendModal("§lSubWeaponShop(サブ武器屋)", "{$result}\nショップを引き続き利用しますか?", "はい", "終了する", 1, 0);
				return true;

			case 11:
				$content = [];
				$content[] = ["type" => "dropdown", "text" => "商品として追加する武器の武器種を選択してください\n値段の編集をする場合は編集する武器の武器種を選択してください\n\n武器種", "options" => WeaponManager::getNames()];
				$data = [
					'type'=>'custom_form',
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => $content
				];
				$cache = [12];
				break;

			case 12:
				$this->weaponType = WeaponManager::getIds()[$this->lastData[0]];
				$content = [];
				$text = "商品として追加する、または値段を変更する武器の武器IDを選択し、値段を入力してください\n武器種>>" . WeaponManager::getNames()[$this->lastData[0]] . "\n武器ID";
				$array = [];
				foreach (array_keys(WeaponManager::getAllData($this->weaponType)) as $key => $value) {
					$array[] = (string) $value;
				}
				$content[] = ["type" => "dropdown", "text" => $text, "options" => $array];
				$content[] = ["type" => "input", "text" => "値段", "placeholder" => "値段を入力"];
				$data = [
					'type'=>'custom_form',
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => $content
				];
				$cache = [13];
				break;

			case 13:
				$provider = SubWeaponShop::get();
				$provider->setPrice($this->weaponType, array_keys(WeaponManager::getAllData($this->weaponType))[$this->lastData[0]], $this->lastData[1]);
				$this->sendModal("§lSubWeaponShop(サブ武器屋)", "設定を反映しました", $label1 = "戻る", $label2 = "閉じる", $jump1 = 1, $jump2 = 0);
				return true;

			case 21:
				$content = [];
				$content[] = ["type" => "dropdown", "text" => "商品として削除する武器の武器種を選択してください\n\n武器種", "options" => WeaponManager::getNames()];
				$data = [
					'type'=>'custom_form',
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => $content
				];
				$cache = [22];
				break;

			case 22:
				$this->weaponType = WeaponManager::getIds()[$this->lastData[0]];
				$content = [];
				$text = "商品として削除する武器の武器IDを選択してください\n武器種>>" . WeaponManager::getNames()[$this->lastData[0]] . "\n武器ID";
				$array = [];
				foreach (array_keys(SubWeaponShop::get()->getItems($this->weaponType)) as $key => $value) {
					$array[] = (string) $value;
				}
				$content[] = ["type" => "dropdown", "text" => $text, "options" => $array];
				$data = [
					'type'=>'custom_form',
					'title'   => "§lSubWeaponShop(サブ武器屋)",
					'content' => $content
				];
				$cache = [23];
				break;

			case 23:
				$provider = SubWeaponShop::get();
				$provider->deleteItem($this->weaponType, array_keys(SubWeaponShop::get()->getItems($this->weaponType))[$this->lastData[0]]);
				$this->sendModal("§lSubWeaponShop(サブ武器屋)", "商品を削除しました", $label1 = "戻る", $label2 = "閉じる", $jump1 = 1, $jump2 = 0);
				return true;

			default:
				$this->close();
				return true;
		}

		if($cache !== []){
			$this->lastSendData = $data;
			$this->cache = $cache;
			$this->show($id, $data);
		}
	}

}