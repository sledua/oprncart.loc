<template id="template-tag-page-cutting">
<div class="cutting">
    <tab-step></tab-step>
    <div class="row">
        <div class="col-md-6">
            <div class="h2">{{$t('common.text_details')}}</div>
            <table class="sawmill-table-edge table table-bordered">
                <thead>
                <tr>
                    <th>{{$t('common.column_width')}}</th>
                    <th>{{$t('common.column_height')}}</th>
                    <th>{{$t('common.column_quantity')}}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(detail, ID) in details" :key="ID">
                    <td class="text-left">
                            {{detail.width}} {{$t('common.text_unit_square_mm')}}
                    </td>
                    <td class="text-left">
                            {{detail.height}} {{$t('common.text_unit_square_mm')}}
                    </td>
                    <td class="text-right">
                            {{detail.quantity}} {{$t('common.text_unit_quantity')}}
                    </td>
                </tr>
                </tbody>
            </table>
              <div class="h2">{{$t('common.text_information')}}</div>
              <div>{{$t('common.text_sum_detail_square')}} {{square/1000000}}  {{$t('common.text_unit_square')}}</div>
              <div>{{$t('common.text_sum_detail_quantity')}} {{quantity}}  {{$t('common.text_unit_quantity')}}</div>
        </div>
        <div class="col-md-6">
            <div class="cutting-setting">
                <div class="h2 text-center">{{$t('common.text_cutting_setting')}}</div>
                <div class="edge_trimming">
                    <div class="h3 text-center">{{$t('common.text_edge_trimming')}}</div>
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" v-model="edge_left"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-1">
                                <input type="text" class="form-control" v-model="edge_top"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>
                            <div class="cutting-square"></div>
                            <div class="input-group mt-1">
                                <input type="text" class="form-control" v-model="edge_bottom"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                               <input type="text" class="form-control" v-model="edge_right"/>
                                <span class="input-group-addon">{{$t('common.text_unit_square_mm')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="control-label col-md-2">{{$t('common.text_kerf_thickness')}}</label>
                            <div class="col-md-10">
                                <select v-model="kerf_thickness" class="form-control">
                                    <option v-for="value in kerf_options" :key="value.value"  :value="value.value">{{value.text}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col text-left"><button class="btn btn-primary">{{$t('common.button_save')}}</button></div>
                    <div class="col text-right"><button class="btn btn-default">{{$t('common.button_cancel')}}</button></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-primary" @click="handleNextStep">{{$t('common.button_next')}}</a>
        </div>
    </div>
</div>
</template>