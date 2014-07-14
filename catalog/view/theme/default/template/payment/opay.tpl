<div class="content">
	<form action="<?php echo $action; ?>" method="post" id="wtp-checkout">
        <?php if($show_channels == 1 and !empty($channels)): ?>
            <div>
                <?php foreach ($channels as $channelGroup): ?>
                	<div><h3><?= $channelGroup['group_title'];?></h3></div>
            		<ul>
						<?php foreach ($channelGroup['channels'] as $channel): ?>
		                	<li class="third" style="padding: 0; margin: 0; margin-bottom:10px; list-style: none; display: block; float: left; width: 214px; border-right: 1px solid #CCC; min-height: 82px; text-align: center;">
		                        <label for="<?= $channel['channel_name'];?>" title="<?= $channel['title'];?>" class="selectable-radio" style="display: block; padding: 10px 0 0 0; width: 200px; min-height: 68px; border: 2px solid #FFF; cursor: pointer;">
		                            <input type="radio" name="channel" id="<?= $channel['channel_name'];?>" title="<?= $channel['title'];?>" value="<?= $channel['channel_name'];?>">
		                            
		                            <img src="<?= $channel['logo_urls']['color_49px'];?>" alt="<?= $channel['title'];?>" class="banklink" style="display: block; margin: 10px auto 0 auto;"> </label>
		                    </li>
						<?php endforeach ?>
		    		<div style="clear:both;"></div>
            		</ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
	</form>
</div>
<div class="buttons">
    <table style="width: 100%;">
        <tr>
            <td align="right">
                <a onclick="$('#wtp-checkout').submit();" class="button">
                	<span><?php echo $button_confirm; ?></span>
                </a>
            </td>
        </tr>
    </table>
</div>
