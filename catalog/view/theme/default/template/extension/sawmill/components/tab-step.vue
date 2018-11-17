<template id="template-tag-tab-step">
<div class="tab-step">
    <ul class="nav nav-tabs">
    <li :class="{active: step === 'specification'}"><a @click="handleStep('specification')">{{$t('common.text_step_specification')}}</a></li>
    <li :class="{active: step === 'cutting'}"><a @click="handleStep('cutting')">{{$t('common.text_step_cutting')}}</a></li>
    <li :class="{active: step === 'services'}"><a @click="handleStep('services')">{{$t('common.text_step_services')}}</a></li>
    </ul>
</div>
</template>