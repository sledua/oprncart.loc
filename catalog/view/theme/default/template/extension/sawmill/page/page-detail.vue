<template id="template-tag-page-detail">
    <div class="step-detail">
        <tab-step></tab-step>
        <div class="row">
            <div class="col-md-12">
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
                    <tr v-for="(product, key) in products" :class="{active: product.active}" :key="key"
                        @click="handleSelectProduct(product.product_id)">
                        <td class="text-center">
                            <img :src="product.image" alt="product.name" :title="product.name" class="img-thumbnail"/>
                        </td>
                        <td class="text-left">{{product.name}}</td>
                        <td class="text-left">{{product.model}}</td>
                        <td class="text-right">{{product.price}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <table class="sawmill-table-edge table table-bordered">
                    <thead>
                    <tr>
                        <th>{{$t('common.column_width')}}</th>
                        <th>{{$t('common.column_height')}}</th>
                        <th>{{$t('common.column_quantity')}}</th>
                        <th>{{$t('common.column_square')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(detail, key) in details" :key="key">
                        <td class="text-left">
                            <input type="text" class="form-control" :name="'detail.'+key+'width'" :value="detail.width" @change="handleEditWidth($event, key)"/>
                        </td>
                        <td class="text-left">
                            <input type="text" class="form-control" :name="'detail.'+key+'height'" :value="detail.height"  @change="handleEditHeight($event, key)"/>
                         </td>
                        <td class="text-right">
                            <input type="text" class="form-control" :name="'detail.'+key+'quantity'" :value="detail.quantity"  @change="handleEditQuantity($event, key)"/>
                        </td>
                        <td class="text-right">
                            {{detail.width * detail.height}}
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">
                            <button @click="addDetail" class="btn btn-primary">Add Detail</button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6"></div>
        </div>
        <div class="row text-right">
            <div class="col-md-12">
                <a class="btn btn-primary" @click="handleNextStep">{{$t('common.button_next')}}</a>
            </div>
        </div>
    </div>
</template>