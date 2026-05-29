<div class="slider-menu">
{if $aeihomeslider.slides}
<div id="slideshow">
	<div class="slideshow-container" data-interval="{$aeihomeslider.speed}" data-wrap="true"  data-pause="{$aeihomeslider.pause}">
		
		<ul class="slides aeisliders">
			{foreach from=$aeihomeslider.slides item=slide name=slides}
			    <li class="slide">
			        <img src="{$slide.image_url}" alt="{$slide.legend}" title="{$slide.title}" 
			             {if $smarty.foreach.slides.first}fetchpriority="high" loading="eager"{/if} />
			        {if $slide.title || $slide.description }
			        <span class="slider-text caption">	
			            <h2>{$slide.title}</h2>
			            <div class="caption-description">
			                {$slide.description nofilter}
			            </div>
			        </span>	
			        {/if}					
			    </li>
			{/foreach}
		</ul>
	</div>
</div>
{/if}
</div>
