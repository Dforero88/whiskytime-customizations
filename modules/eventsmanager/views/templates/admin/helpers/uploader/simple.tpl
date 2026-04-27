{*
 * Events Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2017 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   eventsmanager
*}

<div class="panel col-lg-12">
	<h3>{l s='Add Picture' mod='eventsmanager'}</h3>
	<p class="alert alert-info">{l s='The file must be image.' mod='eventsmanager'}</p>
	<div class="form-group">
		<label for="file" class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Upload image from your computer.' mod='eventsmanager'}">
				{l s='image file' mod='eventsmanager'}
			</span>
		</label>
		<div class="col-lg-9">
			<div class="row">
				<div class="col-lg-7">
             	{if !empty($image)}{$image nofilter}{*HTML Content*}{/if}
					<input id="file" type="file" name="event_image" class="hide" />
					<div class="dummyfile input-group">
						<span class="input-group-addon"><i class="icon-file"></i></span>
						<input id="file-name" type="text" class="disabled" name="filename" readonly />
						<span class="input-group-btn">
							<button id="file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
								<i class="icon-folder-open"></i> {l s='Choose a file' mod='eventsmanager'}
							</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#file-selectbutton').click(function(e){
			$('#file').trigger('click');
		});
		$('#file-name').click(function(e){
			$('#file').trigger('click');
		});
		$('#file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#file-name').val(file[file.length-1]);
		});
	});
</script>
