<div class="col-sm-12">
    <div class="box box-solid"> <!--Sale info box start-->
        <div class="box-header">
            <h3 class="box-title">@lang('business.system')</h3>
        </div>
        <div class="box-body">
             <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <div class="checkbox">
                          <label>
                            {!! Form::checkbox('enable_tooltip', 1, $business->enable_tooltip , 
                            [ 'class' => 'input-icheck']); !!} {{ __( 'business.show_help_text' ) }}
                          </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>