var ei = (function() {

    //allows for the af object to trigger and listen to custom events.
    riot.observable(this);

    /**
    *   createStore. Initialize your app. This will add the default value to the
    * state. Refer to Redux http://redux.js.org/docs/api/Store.html
    */
    this.createStore = function(state){
        this.state = state;
        //allows the af.init(store) to be passed into the mixins model value.
        return this;
    }

    /**
    *   UpdateState. A wrapper function to update the state and call riot update.
    */
    this.updateState = function(data){

        this.state = $.extend(true, this.state, data);
        riot.update(); //will start a full update of all tags
    }

    /**
    *   GetState. Returns the state.
    */
    this.getState = function(){
        return this.state;
    }

    /**
    *   Redux:dispatch function. A wrapper function for triggering a custom event with a
    * updated state value.
    */
    this.dispatch = function(action, state){
        this.trigger(action, state);
    }


    this.getToken = function(){
        if(getURLVar('token')){
            return 'token='+getState().token;
        }

        if(getURLVar('user_token')){
            return 'user_token='+getState().token
        }
    }

    this.export = function(){

        var send_data = $('#form-excel').serializeJSON();

        send_data['source'] = this.getState().source;

        if(typeof getState().selected_filters[getState().source] != undefined){
            send_data['filters'] = getState().selected_filters[getState().source];
        }
        $.ajax({
            url:'index.php?route=extension/d_export_import/excel/export&'+this.getToken(),
            data:send_data,
            dataType:'json',
            context:this,
            type:'post',
            success:function(json){
                if(json['error']){
                    $('.progress-info').html(json['error']);
                    this.finish();
                }
                else if(json['success']){
                    $('#modal-progress').modal('hide');
                    location.href="index.php?route=extension/d_export_import/excel/download&"+this.getToken()+"&source="+this.getState().source;
                    this.finish();
                }
                else{
                    this.export();
                }
            },
            error:function(){
                this.finish();
            }
        });
    }

    this.import = function(){
        var formData = new FormData($('form#form-excel')[0]);
        var that = this;
        $.ajax({
            url: 'index.php?route=extension/d_export_import/excel/import&'+getToken(),
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData:false, 
            datatype:'json',
            success: function (json) {
                that.updateState(json);

                that.finish();
            },
            error:function(){
                that.finish();
            }
        });
    }

    this.updateData = function(){
        if(getState()['start_time'] != null){
            $.ajax({
                url:this.getState().server+'view/javascript/d_export_import/progress_info.json?r='+Math.random(),
                dataType:'json',
                context:this,
                type:'get',
                success:function(json){

                    var time_passed = this.getCurrentTime() - this.getState()['start_time'];
                    
                    if(json.progress != 0){
                        json['time_left'] = msToTime(100*time_passed/json.progress - time_passed);
                    }
                    
                    this.updateState(json);
                },
                complete:function(){
                    setTimeout(function (){
                        this.updateData();
                    }, 1000);
                }
            });
        }
    }

    this.openModal = function(){
        $('#modal-progress').modal({
            backdrop: 'static',
            keyboard: false,
            show:true
        });
    }

    this.initStart = function(){
        data = {
            'start_time':this.getCurrentTime(), 
            'progress':0, 
            'success': null, 
            'current': null, 
            'current_sheet': null, 
            'current_file': null, 
            'error':null,
            'time_left':null
        };

        this.updateState(data);
        this.updateData();
    }

    this.finish = function(){
        this.updateState({'start_time':null});
    }
    this.getCurrentTime = function(){
        var d = new Date();
        var n = d.getTime();
        return n;
    }

    this.msToTime = function(s) {

        function pad(n, z) {
            z = z || 2;
            return ('00' + n).slice(-z);
        }

        var ms = s % 1000;
        s = (s - ms) / 1000;
        var secs = s % 60;
        s = (s - secs) / 60;
        var mins = s % 60;
        var hrs = (s - mins) / 60;

        var str =  pad(hrs) + ':' + pad(mins) + ':' + pad(secs);
        return str;
    }

    // this returns the object that can therefore be extended
    return this;
})();

/**
 *  Alias for d_export_import
 */
 var d_export_import = ei;