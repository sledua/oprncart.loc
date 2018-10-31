<template id="template-tag-page-detail">
    <div class="step-detail">
        <tab-step></tab-step>
        <div class="row">
            <div class="col-md-6">
                <form class="form-horizontal" @submit.prevent="onSubmit">
                    <div class="form-group">
                        <label for="input-detail-name" class="control-label col-md-2">Detail Name</label>
                        <div class="col-md-10">
                            <input type="text" id="input-detail-name" class="form-control" v-model="detail.name" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-material" class="control-label col-md-2">Material</label>
                        <div class="col-md-10">
                            <select id="input-detail-material" class="form-control" v-model="detail.material_id" required>
                                <option value="">--NONE--</option>
                                <option :value="value.product_id" v-for="(value, index) in products" :key="index">
                                    {{value.name}}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-width" class="control-label col-md-2">Detail Width</label>
                        <div class="col-md-10">
                            <input type="text" id="input-detail-width" class="form-control" v-model="detail.width" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-height" class="control-label col-md-2">Detail Height</label>
                        <div class="col-md-10">
                            <input type="text" id="input-detail-height" class="form-control" v-model="detail.height" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-quantity" class="control-label col-md-2">Detail Quantity</label>
                        <div class="col-md-10">
                            <input type="text" id="input-detail-quantity" class="form-control"
                                   v-model="detail.quantity" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-multiplicity_stitching" class="control-label col-md-2">The multiplicity of
                            stitching</label>
                        <div class="col-md-10">
                            <input type="text" id="input-detail-multiplicity_stitching" class="form-control"
                                   v-model="detail.multiplicity_stitching" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-detail-take_into_account_texture" class="control-label col-md-2">Take into account the texture</label>
                        <div class="col-md-10">
                            <select id="input-detail-take_into_account_texture" class="form-control" v-model="detail.take_into_account_texture" required>
                                <option :value="index" v-for="(value, index) in textureOptions" :key="index">{{value}}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Detail</button>
                </form>
            </div>
            <div class="col-md-6">
                <table class="sawmill-table-edge table table-bordered">
                    <thead>
                    <tr>
                        <th>{{$t('common.column_name')}}</th>
                        <th>{{$t('common.column_size')}}</th>
                        <th>{{$t('common.column_quantity')}}</th>
                        <th>{{$t('common.column_take_texture')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(detail, key) in details" :key="key">
                        <td class="text-left">{{detail.product.name}}</td>
                        <td class="text-left">{{detail.width}}x{{detail.height}}</td>
                        <td class="text-right">{{detail.quantity}}</td>
                        <td class="text-right">{{detail.take_into_account_texture ? $t('common.text_yes') : $t('common.text_no')}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row text-right">
            <div class="col-md-12">
                <a class="btn btn-primary" @click="handleNextStep">{{$t('common.button_next')}}</a>
            </div>
        </div>
    </div>
</template>