<template id="template-tag-tab-step">
<div class="tab-step">
    <ul class="nav nav-tabs">
    <li :class="{active: step === 'detail'}"><a @click="handleStep('detail')">{{$t('common.text_step_detail')}}</a></li>
    <li :class="{active: step === 'additional'}"><a @click="handleStep('additional')">{{$t('common.text_step_additional')}}</a></li>
    <li :class="{active: step === 'calculate'}"><a @click="handleStep('calculate')">{{$t('common.text_step_calculate')}}</a></li>
    <li :class="{active: step === 'order'}"><a @click="handleStep('order')">{{$t('common.text_step_order')}}</a></li>
    </ul>
</div>
</template>