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

class item_AaTest1 extends itemTemplate
{
	function getItemDetails()
	{
		// Used Daniel15's item template and modified it for added utilities
		$this->authorName = 'Turbo Nezir';
		$this->authorWeb = 'http://www.buyucedunya-rpg.com/';
		$this->authorEmail = 'Turbo Nezir#0042';

		$this->name = 'custom_stat_roll';
		$this->desc = 'Increase Karma and Roll a Custom Profile Field Stat Dice';
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
		
		// Increase Karma here
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET `karma_good` = `karma_good` + {int:amount}
			WHERE id_member = {int:id}',
			array(
				'id' => $context['user']['id'],
				'amount' => $item_info[1],
			));
			
		// Start SMF Shop log mod
			global $boarddir;
			$filename  = $boarddir . "/shopkarmaitemlog.txt";
			$data = "[" . date("g:ia m/d/Y")."] " . $context['user']['name']. " ---> +1 GP from Item" . "\n";
			file_put_contents($filename,$data,FILE_APPEND); 
		// end SMF Shop Log Mod
		
		// This part fetches user's Custom Profile Field value that is required
		$result = $smcFunc['db_query']('', "
				SELECT value
				FROM {db_prefix}themes
				WHERE id_member = {int:id} AND variable LIKE '%cust_ksirk%'", // "cust_ksirk" shall be your own Custom Field name in database
				array(
					'id' => $context['user']['id'],
				));
		$row = $smcFunc['db_fetch_assoc']($result);
		// You can apply arithmetics to the value obtained by casting it to an integer
		$potions_bonus = intval(intval($row['value']) / 10);
		
		// We randomize the success chance and add our Custom Field bonus to decide final result
		$dice = mt_rand(1, 10);
		$final_result = $dice + $potions_bonus;
		
		// If the random chance result + Custom Field Score is higher than the threshold, something happens.
		// This item can be used with "Increase Karma and Create New Item in Inventory" item for adding a "Felix Felicis" if the attempt is successful.
		if ($final_result < 6)
        	return ' You tried to brew Felix Felicis. Your dice: ' . $dice . '. Result with your Potions Skill: ' . $final_result . ". You failed, unfortunately.";
        else
            return ' You tried to brew Felix Felicis. Your dice: ' . $dice . '. Result with your Potions Skill: ' . $final_result . ". You successfully brewed a vial of Felix Felicis!";
	}
}

?>
