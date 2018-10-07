<template id="template-tag-page-detail">
<div class="step-detail">
    <tab-step></tab-step>
    <div class="row">
        <div class="col-md-6">
            detail
        </div>
        <div class="col-md-6">
            detail
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-primary" @click="handleNextStep">{{$t('common.button_next')}}</a>
        </div>
    </div>
</div>
</template>