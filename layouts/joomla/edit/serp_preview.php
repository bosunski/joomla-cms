<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
?>

<div id="snippet-box" class="margin-below" style="display: block;">
	<div>
		<div id="app-snippet-title" class="snippet-title">
			<font style="vertical-align: inherit;">
				<font style="vertical-align: inherit;">{{ articleTitle || 'Title will appear here' }}</font>
			</font>
		</div>
	</div>
	<div>
		<div id="app-snippet-url" class="snippet-url">
<!--			<font style="vertical-align: inherit;">-->
<!--				<font style="vertical-align: inherit;">bosunski.fun</font>-->
<!--			</font>-->
		</div>
	</div>
<!--	<div id="app-snippet-rich" class="snippet-rich" style="clear: left; font-size: 13px; display: block;">-->
<!--		<img src="https://app.sistrix.com/web/images/svg/star.svg" alt="" style="width: 14px; height: 14px; position: relative; top: -2px;">-->
<!--		<img src="https://app.sistrix.com/web/images/svg/star.svg" alt="" style="width: 14px; height: 14px; position: relative; top: -2px;">-->
<!--		<img src="https://app.sistrix.com/web/images/svg/star.svg" alt="" style="width: 14px; height: 14px; position: relative; top: -2px;">-->
<!--		<img src="https://app.sistrix.com/web/images/svg/star.svg" alt="" style="width: 14px; height: 14px; position: relative; top: -2px;">-->
<!--		<img src="https://app.sistrix.com/web/images/svg/star.svg" alt="" style="width: 14px; height: 14px; position: relative; top: -2px;">-->
<!--		<font style="vertical-align: inherit;">-->
<!--			<font style="vertical-align: inherit;">-->
<!--				Rating: 4.1 - 1.488 reviews-->
<!--			</font>-->
<!--		</font>-->
<!--	</div>-->
	<div class="snippet-text">
<!--		<span id="app-snippet-author" style="display: inline;">-->
<!--			<font style="vertical-align: inherit;">-->
<!--				<font style="vertical-align: inherit;">John Doe -</font>-->
<!--			</font>-->
<!--		</span>-->
<!--		<span id="app-snippet-date" style="display: inline;">-->
<!--			<font style="vertical-align: inherit;">-->
<!--				<font style="vertical-align: inherit;">31.03.2019 -</font>-->
<!--			</font>-->
<!--		</span>-->
		<span id="app-snippet-text" class="snippet-text" style="display: inline;">
			<font style="vertical-align: inherit;">
				<font style="vertical-align: inherit;">{{ articleDescription || 'Article description will appear here!'}}</font>
			</font>
		</span>
	</div>
</div>

<h3>Title: {{ titlePixelLength + 'px' }}</h3>
<div class="progress" style="height: 6px; border-radius: 0px 0px 2px 2px;">
	<div role="progressbar" id="app-snippet-title-progress" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" :class="titleBarActiveClass" :style="{width: titlePixelLength + 'px'}">

	</div>
</div>

<h3>Description: {{ descriptionPixelLength + 'px' }}</h3>
<div class="progress" style="height: 6px; border-radius: 0px 0px 2px 2px;">
	<div role="progressbar" id="app-snippet-title-progress" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" :class="descriptionBarActiveClass" :style="{width: descriptionPixelLength + 'px'}">

	</div>
</div>

<span ref="titleRuler" id="titleRuler"></span>
<span ref="descriptionRuler" id="descriptionRuler"></span>
