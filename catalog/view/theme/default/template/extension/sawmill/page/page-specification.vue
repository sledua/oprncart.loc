<template id="template-tag-page-specification">
    <div class="step-specification">
        <tab-step></tab-step>
        <div class="row">
            <div class="col-md-12">
                <table class="sawmill-table-edge table table-bordered">
                    <thead>
                    <tr>
                        <th>{{$t('common.column_image')}}</th>
                        <th>{{$t('common.column_name')}}</th>
                        <th>{{$t('common.column_details')}}</th>
                        <th>{{$t('common.column_quantity')}}</th>
                        <th>{{$t('common.column_price')}}</th>
                        <th>{{$t('common.column_total')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(product, key) in products" :class="{active: product.active}" :key="key"
                        @click="handleSelectProduct(product.product_id)">
                        <td class="text-center">
                            <img :src="product.image" alt="product.name" :title="product.name" class="img-thumbnail"/>
                        </td>
                        <td class="text-left">{{product.name}}</td>
                        <td class="text-left">{{product.detailsQuantity}} {{$t('common.text_unit_quantity')}}</td>
                        <td class="text-left">{{product.detailsSquare/1000000}} {{$t('common.text_unit_square')}}</td>
                        <td class="text-right">{{product.price}}</td>
                        <td class="text-right">{{product.detailsSquare/1000000 * product.price}}</td>
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
                    <tr v-for="(detail, ID) in details" :key="ID">
                        <td class="text-left">
                            <div class="input-group">
                                <input type="text" class="form-control" :name="'detail.'+ID+'width'" :value="detail.width"
                                       @change="handleEditWidth($event, ID)"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>

                        </td>
                        <td class="text-left">
                            <div class="input-group">
                                <input type="text" class="form-control" :name="'detail.'+ID+'height'" :value="detail.height"
                                       @change="handleEditHeight($event, ID)"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>

                        </td>
                        <td class="text-right">
                            <div class="input-group">
                                <input type="text" class="form-control" :name="'detail.'+ID+'quantity'"
                                       :value="detail.quantity" @change="handleEditQuantity($event, ID)"/>
                                <span class="input-group-addon">{{$t('common.text_unit_quantity')}}</span>
                            </div>

                        </td>
                        <td class="text-right">
                            {{detail.width * detail.height}}  {{$t('common.text_unit_square')}}
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
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="h2">{{$t('common.text_information')}}</div>
                        <div>{{$t('common.text_sum_detail_square')}} {{square/1000000}}  {{$t('common.text_unit_square')}}</div>
                        <div>{{$t('common.text_sum_detail_quantity')}} {{quantity}}  {{$t('common.text_unit_quantity')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>