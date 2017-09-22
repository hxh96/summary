/**
 * 视频上传功能
 */
$(function() {
    $('#file_uploadfile').uploadify({
        'swf'      : SCOPE.ajax_upload_swf,
        'uploader' : SCOPE.ajax_upload_file_url,
        'buttonText': '上传文件',
        'fileObjName' : 'uploadfile',
        'fileSizeLimit':'0',
        //允许上传的文件后缀
        'fileTypeExts': '*.mp4; *.flv; *.avi; *.doc; *.xls; *.xlsx; *.txt; *.pdf; *.mp3; *.wav; *.avi; *.jpg; *.png; *.gif; *.docx; *.ppt; *.htm; *.html; *.zip; *.rar; *.gz; *.bz2; *.wmv; *.wma; *.mid; *.mpg; *.asf; *.rm; *.rmvb; *.swf; *.jpeg; *.bmp; *.f4v;',
        'onUploadSuccess' : function(file,data,response) {
            // response true ,false
            if(response) {
                var obj = JSON.parse(data); //由JSON字符串转换为JSON对象

                console.log(data);
                $('#' + file.id).find('.data').html(' 上传完毕');
                //return dialog.toconfirm('上传完毕');
                
                $("#file_upload_file").attr('value',obj.data);
            }else{
                //alert('上传失败');
                return dialog.error('上传失败');
            }
        },
    });
});