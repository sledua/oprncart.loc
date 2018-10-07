<template id="template-tag-sawmill">
<div class="sawmill">
    <div class="sawmill-popup-backdrop" v-if="showPopup"  @click="handleOpen"></div>
    <div class="sawmill-popup" v-if="showPopup">
        <div :is="'page-'+step"></div>
    </div>
    <button type="button" id="button-calc" @click="handleOpen" class="btn btn-primary btn-lg btn-block">Calculate</button>
</div>
</template>