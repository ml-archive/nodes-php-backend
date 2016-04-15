<div {!! isset($image) ? 'data-image="' . $image . '"' : '' !!}
     {!! isset($file) ? 'data-file="' . $file . '"' : '' !!}
     {!! !empty($disablePreview) ? 'data-disable-preview="' . $disablePreview . '"' : '' !!}
    class="form-group input-group file-picker">
    <label for="backendUserFormImage">{{ $label }}</label>
    <div class="file-picker__inner">
        <div class="file-picker__zone">
            <img src="" class="file-picker__preview img-responsive img-thumb">
            <i class="file-picker__icon fa"></i>
            <span class="file-picker__drop-text">Drop your file here</span>

            <button class="btn btn-transparent btn-sm file-picker__clear">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="file-picker__input-group">
            {!! Form::file($elementName, ['id' => !empty($elementId) ? $elementId : 'backendUserFormImage', 'class' => 'file-picker__file-input']) !!}
            <div class="input-group">
                    <span class="input-group-btn">
                        <span class="file-input__choose btn btn-default">
                            Browse
                        </span>
                    </span>
                <input type="text" class="form-control file-picker__file-name" readonly="">
            </div>
        </div>
    </div>
</div>
