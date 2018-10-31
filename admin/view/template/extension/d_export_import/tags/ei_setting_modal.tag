<ei_setting_modal>
<div id="modal-setting" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{getState().translate.text_filter_title}</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody if={getState().selected_filters[getState().filter_active]}>
                        <tr each={filter, filter_index in getState().selected_filters[getState().filter_active]}>
                            <td>
                                <select class="form-control" onchange={change_column}>
                                    <option each={item in getState().filters[getState().filter_active]} data-concat={item.concat? item.concat : 0} value="{getFilterValue(item)}">{item.name}</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control" onchange={change_condition}>
                                    <option each={condition, index in getState().conditions} value="{index}">{condition}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="{filter.value}" onchange={change_value}/>
                            </td>
                            <td class="col-sm-1 text-right"><a onclick={removeFilter} class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>
                        </tr>
                    </tbody>
                    <tbody if={!getState().selected_filters[getState().filter_active] || getState().selected_filters[getState().filter_active].length == '0'}>
                        <tr><td colspan="4" class="text-center">{getState().translate.text_not_selected}</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"></td>
                            <td class="col-sm-1 text-right"><a onclick={addFilter} class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{getState().translate.button_close}</button>
            </div>
        </div>
    </div>
</div>
<script>
    change_column(e){
        filter_active = getState().filter_active;
        filters = getState().selected_filters;

        filters[filter_active][e.item.filter_index].concat = $(e.target).find('option:selected').data('concat')

        filters[filter_active][e.item.filter_index]['column'] = $(e.target).val();
        updateState({'selected_filters': filters});
    }
    change_condition(e){
        filter_active = getState().filter_active;
        filters = getState().selected_filters;
        filters[filter_active][e.item.filter_index]['condition'] = $(e.target).val();
        updateState({'selected_filters': filters});
    }
    change_value(e){
        filter_active = getState().filter_active;
        filters = getState().selected_filters;
        filters[filter_active][e.item.filter_index]['value'] = $(e.target).val();
        updateState({'selected_filters': filters});
    }
    getFilterValue(item) {
        var result = ''
        if(item.concat) {
            result = '(SELECT `'+item.table.name + '`.`' + item.table.key+'` FROM `[[db_prefix]]'+item.table.full_name+'` '+item.table.name + ' WHERE `'+item.table.name+'`.'+item.column+' [[condition]] \'[[value]]\')';
        } else {
            result = '`'+item.table + '`.`' + item.column+'`'
        }

        return result
    }
    addFilter(e){
        filter_active = getState().filter_active;
        filters = getState().selected_filters;

        if(typeof getState().filters[getState().filter_active][0] != undefined){
            column = '`'+getState().filters[getState().filter_active][0]['table']+'`.`'+getState().filters[getState().filter_active][0]['column']+'`';
        }
        else{
            column = '';
        }

        if(typeof filters[filter_active] !== 'undefined'){
            filters[filter_active].push({
                value : '',
                condition : '=',
                column : column,
                concat: 0
            });
        }
        else{
            filters[filter_active] = [{
                value : '',
                condition : '=',
                column : column,
                concat: 0
            }];
        }
        updateState({'selected_filters': filters});
    }
    removeFilter(e){
        filter_active = getState().filter_active;
        filters = getState().selected_filters;
        filters[filter_active].splice(e.item.filter_index, 1);
        updateState({'selected_filters': filters});
    }
</script>
</ei_setting_modal>