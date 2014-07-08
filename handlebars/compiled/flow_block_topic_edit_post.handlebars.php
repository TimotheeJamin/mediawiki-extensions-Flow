<?php return function ($in, $debugopt = 1) {
    $cx = Array(
        'flags' => Array(
            'jstrue' => false,
            'jsobj' => false,
            'spvar' => true,
            'debug' => $debugopt,
        ),
        'helpers' => Array(            'l10n' => 'Flow\TemplateHelper::l10n',
            'html' => 'Flow\TemplateHelper::html',
            'l10nParse' => 'Flow\TemplateHelper::l10nParse',
            'linkWithReturnTo' => 'Flow\TemplateHelper::linkWithReturnTo',
),
        'blockhelpers' => Array(),
        'hbhelpers' => Array(            'eachPost' => 'Flow\TemplateHelper::eachPost',
            'ifAnonymous' => 'Flow\TemplateHelper::ifAnonymous',
            'tooltip' => 'Flow\TemplateHelper::tooltip',
),
        'scopes' => Array($in),
        'sp_vars' => Array(),

    );
    return '<div class="flow-board">
	<div class="flow-error-container">
'.((LCRun3::ifvar($cx, ((is_array($in) && isset($in['errors'])) ? $in['errors'] : null))) ? '
	<div class="flow-errors errorbox">
		<ul>
			'.LCRun3::sec($cx, ((is_array($in) && isset($in['errors'])) ? $in['errors'] : null), $in, true, function($cx, $in) {return '
				<li>'.LCRun3::ch($cx, 'html', Array(((is_array($in) && isset($in['message'])) ? $in['message'] : null)), 'encq').'</li>
			';}).'
		</ul>
	</div>
' : '').'
</div>


	

	'.LCRun3::sec($cx, ((is_array($in) && isset($in['roots'])) ? $in['roots'] : null), $in, true, function($cx, $in) {return '
		'.LCRun3::hbch($cx, 'eachPost', Array($cx['scopes'][0],$in), $in, function($cx, $in) {return '
			<form class="flow-edit-post-form" method="POST" action="'.htmlentities(((is_array($in['actions']['edit']) && isset($in['actions']['edit']['url'])) ? $in['actions']['edit']['url'] : null), ENT_QUOTES, 'UTF-8').'">
	<div class="flow-error-container">
'.((LCRun3::ifvar($cx, ((is_array($in) && isset($in['errors'])) ? $in['errors'] : null))) ? '
	<div class="flow-errors errorbox">
		<ul>
			'.LCRun3::sec($cx, ((is_array($in) && isset($in['errors'])) ? $in['errors'] : null), $in, true, function($cx, $in) {return '
				<li>'.LCRun3::ch($cx, 'html', Array(((is_array($in) && isset($in['message'])) ? $in['message'] : null)), 'encq').'</li>
			';}).'
		</ul>
	</div>
' : '').'
</div>

	<input type="hidden" name="wpEditToken" value="'.htmlentities(((is_array($cx['scopes'][0]) && isset($cx['scopes'][0]['editToken'])) ? $cx['scopes'][0]['editToken'] : null), ENT_QUOTES, 'UTF-8').'" />
	<input type="hidden" name="topic_prev_revision" value="'.htmlentities(((is_array($in) && isset($in['revisionId'])) ? $in['revisionId'] : null), ENT_QUOTES, 'UTF-8').'" />
	'.LCRun3::hbch($cx, 'ifAnonymous', Array(), $in, function($cx, $in) {return '
		'.LCRun3::hbch($cx, 'tooltip', Array('positionClass'=>'down','contextClass'=>'progressive','extraClass'=>'flow-anon-warning flow-form-collapsible','isBlock'=>((is_array($in) && isset($in['true'])) ? $in['true'] : null)), $in, function($cx, $in) {return '
	'.LCRun3::ch($cx, 'l10nParse', Array('flow-anon-warning',LCRun3::ch($cx, 'linkWithReturnTo', Array('Special:UserLogin'), 'encq'),LCRun3::ch($cx, 'linkWithReturnTo', Array('Special:UserLogin/signup'), 'encq')), 'encq').'';}).'
	';}).'

	<textarea name="topic_content" class="mw-ui-input flow-form-collapsible"
		data-flow-preview-template="flow_post"
		data-role="content">'.((LCRun3::ifvar($cx, ((is_array($cx['scopes'][0]['submitted']) && isset($cx['scopes'][0]['submitted']['content'])) ? $cx['scopes'][0]['submitted']['content'] : null))) ? ''.htmlentities(((is_array($cx['scopes'][0]['submitted']) && isset($cx['scopes'][0]['submitted']['content'])) ? $cx['scopes'][0]['submitted']['content'] : null), ENT_QUOTES, 'UTF-8').'' : ''.htmlentities(((is_array($in['content']) && isset($in['content']['content'])) ? $in['content']['content'] : null), ENT_QUOTES, 'UTF-8').'').'</textarea>

	<div class="flow-form-actions flow-form-collapsible">
		<button class="flow-ui-button flow-ui-constructive"
		        data-flow-api-handler="submitEditPost">'.LCRun3::ch($cx, 'l10n', Array('flow-post-action-edit-post-submit'), 'encq').'</button>
		<button data-flow-api-handler="preview"
        data-flow-api-target="< form textarea"
        name="preview"
        data-role="action"
        class="flow-ui-button flow-ui-progressive flow-ui-quiet">'.LCRun3::ch($cx, 'l10n', Array('flow-preview'), 'encq').'</button>
		<button class="flow-ui-button flow-ui-destructive flow-ui-quiet"
		        data-flow-interactive-handler="cancelForm"
				data-role="cancel">'.LCRun3::ch($cx, 'l10n', Array('flow-cancel'), 'encq').'</button>
		<small class="flow-terms-of-use plainlinks">'.LCRun3::ch($cx, 'l10nParse', Array('flow-terms-of-use-edit'), 'encq').'</small>
	</div>
</form>

		';}).'
	';}).'
</div>
';
}
?>