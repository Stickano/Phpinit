<?php

class Emoji {

	/**
	 * Unicode Emojis
	 * @param  int $val 	No. for smiley to return
	 * @return string      all/single
	 */	
	public function umoji($val=null) {
		$emojis = array(
				'(ʘ‿ʘ)',
				'(ʘ_ʘ)',
				'(॓_॔)',
				'(ಠ‿ʘ)',
				'(ಠ‿ಠ)',
				'(ಠ⌣ಠ)',
				'(⊙_◎)',
				'(╯°□°）╯︵ ┻━┻',
				'¯\_(ツ)_/¯',
				'¯＼(º_o)/¯',
				'͡° ͜ʖ﻿ ͡°',
				'•_•)',
				'( •_•)>⌐■-■',
				'(⌐■_■)',
				'━━━ヽ(ヽ(ﾟヽ(ﾟ∀ヽ(ﾟ∀ﾟヽ(ﾟ∀ﾟ)ﾉﾟ∀ﾟ)ﾉ∀ﾟ)ﾉﾟ)ﾉ)ﾉ━━━',
				'┌∩┐(◕_◕)┌∩┐',
				'┌( ಠ_ಠ)┘',
				'┌( ಥ_ಥ)┘',
				'╚(•⌂•)╝',
				'﴾͡๏̯͡๏﴿',
				'(\/) (°,,°) (\/) WOOPwoopwowopwoopwoopwoop!',
				'٩(×̯×)۶',
				'٩(̾●̮̮̃̾•̃̾)۶',
				'٩(-̮̮̃•̃)۶',
				'٩(-̮̮̃-̃)۶'
			);
		if($val != null && is_int($val)){
			$val--;
			return $emojis[$val];
		}else{
			$br = 0;
			foreach ($emojis as $emoji) {
				$br++;
				echo $br.':&nbsp;&nbsp;&nbsp;
					<span style="line-height:1.8em;">
						'.$emoji.'
					</span>
					<br>';
			}
		}
	}

}

?>