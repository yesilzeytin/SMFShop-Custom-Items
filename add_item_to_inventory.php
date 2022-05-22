<?php
/**********************************************************************************
* SMFShop item                                                                    *
***********************************************************************************
* SMFShop: Shop MOD for Simple Machines Forum                                     *
* =============================================================================== *
* Software Version:           SMFShop 3.0 (Build 12)                              *
* $Date:: 2007-04-14 10:39:52 +0200 (za, 14 apr 2007)                           $ *
* $Id:: IncreaseKarma.php 113 2007-04-14 08:39:52Z daniel15                     $ *
* Software by:                DanSoft Australia (http://www.dansoftaustralia.net/)*
* Copyright 2005-2007 by:     DanSoft Australia (http://www.dansoftaustralia.net/)*
* Support, News, Updates at:  http://www.dansoftaustralia.net/                    *
*                                                                                 *
* Forum software by:          Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006-2007 by:     Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version of the license can always be found at                        *
* http://www.simplemachines.org.                                                  *
**********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

class item_AaTest2 extends itemTemplate
{
	function getItemDetails()
	{
		// Used Daniel15's item template and modified it for added utilities
		$this->authorName = 'Turbo Nezir';
		$this->authorWeb = 'http://www.buyucedunya-rpg.com/';
		$this->authorEmail = 'Turbo Nezir#0042';

		$this->name = 'add_item_to_inventory';
		$this->desc = 'Increase Karma and Create New Item in Inventory';
		$this->price = 100;

		$this->require_input = false;
		$this->can_use_item = true;
		$this->addInput_editable = true;
	}
	
	// See 'AddToPostCount.php' for info on how this works
	function getAddInput()
	{
	
		global $user_info, $item_info;
		if ($item_info[1] == 0) $item_info[1] = 5;
		return 'Amount to increase Karma by: <input type="text" name="info1" value="' . $item_info[1] . '" />';
	}

	function onUse()
	{
		global $smcFunc, $context, $item_info;
		
		// Increases Karma here
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET `karma_good` = `karma_good` + {int:amount}
			WHERE id_member = {int:id}',
			array(
				'id' => $context['user']['id'],
				'amount' => $item_info[1],
			));
			
	    // Put a new item in user's inventory
	    // itemid is for any item you would prefer. That shall be updated for any item required.
		// Also a random function within here can generate a loot box.
		$smcFunc['db_insert']('insert', '{db_prefix}shop_inventory',
			array(
				'ownerid' => 'int',
				'itemid' => 'int',
				'amtpaid' => 'float',
				'trading' => 'int',
				'tradecost' => 'float',
				),
			array(
				array(
					'ownerid' => $context['user']['id'],
					'itemid' => 67, // Item ID has to be selected from your own shop accordingly, whichever item you prefer
					'amtpaid' => 0.00,
					'trading' => 0,
					'tradecost' => 0.00,
					),
				),
			array());
	    
		// Start SMF Shop Log mod
			global $boarddir;
			$filename  = $boarddir . "/shopkarmaitemlog.txt";
			$data = "[" . date("g:ia m/d/Y")."] " . $context['user']['name']. " ---> +1 GP from Item" . "\n";
			file_put_contents($filename,$data,FILE_APPEND); 
		// end SMF Shop Log Mod

		return 'Your Karma is increased by ' . $item_info[1] . '! Also a new item has been added to your inventory!';
	}

}

?>
