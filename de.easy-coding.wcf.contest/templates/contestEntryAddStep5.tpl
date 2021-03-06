<script type="text/javascript">
var participants = new Array();
{assign var=i value=0}
{foreach from=$participants item=participant}
	participants[{@$i}] = new Object();
	participants[{@$i}]['name'] = '{@$participant.name|encodeJS}';
	participants[{@$i}]['type'] = '{@$participant.type}';
	participants[{@$i}]['id'] = '{@$participant.id}';
	{assign var=i value=$i+1}
{/foreach}

onloadEvents.push(function() {
	// participants
	var list1 = new ContestPermissionList('participant', participants, 'index.php?page=ContestParticipantObjects');
	
	var suggestion = new ContestSuggestion();
	suggestion.setSource('index.php?page=ContestParticipantSuggest{@SID_ARG_2ND_NOT_ENCODED}');
	suggestion.enableIcon(true);
	suggestion.init('participantAddInput');
	
	// add onsubmit event
	onsubmitEvents.push(function(suggestion) {
		return function(form) {
			if (suggestion.selectedIndex != -1) return false;
			if (list1.inputHasFocus) return false;
			list1.submit(form);
		};
	}(suggestion));
});
</script>

<h3 class="subHeadline">{lang}wcf.contest.{@$action}{/lang}: {lang}wcf.contest.participants{/lang}</h3>
<p>{lang}wcf.contest.participant.owner.description{/lang}</p>
<fieldset>
	<legend>{lang}wcf.contest.participant{/lang}</legend>
	
	<div class="formElement">
		<div class="formFieldLabel" id="participantTitle">
			{lang}wcf.contest.participant.add{/lang}
		</div>
		<div class="formField"><div id="participant" class="accessRights"></div></div>
	</div>
	<div class="formElement">
		<div class="formField">	
			<input id="participantAddInput" type="text" name="" value="" class="inputText accessRightsInput" />
			<input id="participantAddButton" type="button" value="{lang}wcf.contest.participant.add{/lang}" />
		</div>
		<p class="formFieldDesc">{lang}wcf.contest.owner.enter{/lang}</p>
	</div>
		<div class="formElement">
		<div class="formField">
			<label for="comment_trigger">
				<input type="checkbox" name="comment_trigger" id="comment_trigger" value="1" {if $comment_trigger} checked="checked"{/if} onclick="Effect.toggle('comment', 'slide');"/>
				{lang}wcf.contest.participant.create{/lang}
				
				
				<script type="text/javascript">
					//<![CDATA[
					document.observe("dom:loaded", function() {
						if(typeof document.getElementById('comment_trigger').checked == 'undefined' || !document.getElementById('comment_trigger').checked) {
							document.getElementById('comment').style.display = 'none';
						}
					});
					//]]>
				</script>
			</label>
		</div>
		<p class="formFieldDesc">{lang}wcf.contest.participant.create.description{/lang}</p>
	</div>
	<div class="formElement" id="comment">
		<div class="formField">
			<fieldset>
				<legend>{lang}wcf.contest.comment{/lang}</legend>
				<div class="formElement">
					<div class="formFieldLabel">
						{lang}wcf.contest.comment.message{/lang}
					</div>
					<div class="formField">
						<textarea id="commentAddText" type="text" name="commentAddText" rows="5" cols="40"></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
	{if $additionalInformationFields|isset}{@$additionalInformationFields}{/if}
</fieldset>

{if $additionalFields1|isset}{@$additionalFields1}{/if}

<div class="formSubmit">
	<input type="submit" name="back" accesskey="b" value="{lang}wcf.global.button.back{/lang}" tabindex="{counter name='tabindex'}" onclick="return steppedTabMenu.back()" />
	<input type="submit" name="send" accesskey="n" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
	{@SID_INPUT_TAG}
	{@SECURITY_TOKEN_INPUT_TAG}
	<input type="hidden" name="idHash" value="{$idHash}" />
</div>
