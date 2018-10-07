<template id="template-tag-page-edge">
<div class="step-edge">
    <div class="row">
        <div class="col-md-6">
            <div class="sawmill-material-title">{{$t('common.text_material_title')}}</div>
            <div class="sawmill-material-wrapper">
                <div class="sawmill-material-thumb" @click="handleSelectProduct(product.product_id)" :class="{active: product.active, selected: product.selected}" v-for="(product, key) in products" :key="key">
                    <div class="product-name">{{product.name}} x {{product.quantity}} <span class="product-total">{{product.total}}</span></div>
                </div>
            </div>
            <a class="btn btn-primary" :disabled="!isEdgeSelected" @click="handleNextStep">{{$t('common.button_cutting_begin')}}</a>
        </div>
        <div class="col-md-6">
            <div class="sawmill-edge-title">{{$t('common.text_product_framing')}}</div>
            <div class="sawmill-edge-subTitle">{{$t('common.text_edge_recommended')}}</div>
            <table class="sawmill-table-edge table table-bordered">
                <thead>
                    <tr>
                        <th>{{$t('common.column_image')}}</th>
                        <th>{{$t('common.column_name')}}</th>
                        <th>{{$t('common.column_model')}}</th>
                        <th>{{$t('common.column_price')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(product, key) in edgeProducts" :class="{active: product.active}" :key="key" @click="handleSelectEdge(product.product_id)">
                        <td class="text-center">
                            <img :src="product.image" alt="product.name" :title="product.name" class="img-thumbnail" />
                        </td>
                        <td class="text-left">{{product.name}}</td>
                        <td class="text-left">{{product.model}}</td>
                        <td class="text-right">{{product.price}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</template>