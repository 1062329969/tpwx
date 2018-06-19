/*global Qiniu */
/*global plupload */
/*global FileProgress */
/*global hljs */


$(function() {
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',   //上传模式,依次退化
        browse_button: 'pickfiles',      //上传选择的点选按钮，**必需**
        container: 'container',          //上传区域DOM ID，默认是browser_button的父元素，
        drop_element: 'container',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
        max_file_size: '1000mb',
        flash_swf_url: 'bower_components/plupload/js/Moxie.swf',
        dragdrop: true,            //开启可拖曳上传
        chunk_size: '4mb',         //分块上传时，每片的体积
        multi_selection: !(mOxie.Env.OS.toLowerCase()==="ios"),
        uptoken_url: $('#uptoken_url').val(),
        // uptoken_func: function(){
        //     var ajax = new XMLHttpRequest();
        //     ajax.open('GET', $('#uptoken_url').val(), false);
        //     ajax.setRequestHeader("If-Modified-Since", "0");
        //     ajax.send();
        //     if (ajax.status === 200) {
        //         var res = JSON.parse(ajax.responseText);
        //         console.log('custom uptoken_func:' + res.uptoken);
        //         return res.uptoken;
        //     } else {
        //         console.log('custom uptoken_func err');
        //         return '';
        //     }
        // },
        domain: $('#domain').val(),
        get_new_uptoken: false,   //设置上传文件的时候是否每次都重新获取新的token
        // downtoken_url: '/downtoken',
        // unique_names: true,
        // save_key: true,
        // x_vars: {
        //     'id': '1234',
        //     'time': function(up, file) {
        //         var time = (new Date()).getTime();
        //         // do something with 'time'
        //         return time;
        //     },
        // },
        auto_start: true,   //选择文件后自动上传，若关闭需要自己绑定事件触发上传
        log_level: 0,
        init: {
            'FilesAdded': function(up, files)
			{
                
            },
            'BeforeUpload': function(up, file)
			{
                //var progress = new FileProgress(file, 'fsUploadProgress');
                //var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                //if (up.runtime === 'html5' && chunk_size) {
                    //progress.setChunkProgess(chunk_size);
                //}
            },
            'UploadProgress': function(up, file) {
                $('#doing').show();
				$('#progressmain').show();
				$('#filevoide').hide();
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
				var size = plupload.formatSize(file.loaded).toUpperCase();
                var formatSpeed = plupload.formatSize(file.speed).toUpperCase();
                $('#doing').html("已上传: " + size + " 上传速度： " + formatSpeed + "/s");
				$('#progress').width(file.percent + "%");
            },
            'UploadComplete': function()
			{
                $('#success').show();
            },
            'FileUploaded': function(up, file, info) {
                //var progress = new FileProgress(file, 'fsUploadProgress');
                //progress.setComplete(up, info);
				$('#doing').hide();
				$('#progressmain').hide();
				$('#filevoide').show();
				var res = $.parseJSON(info);
                
				var url;
				if (res.url) {
					$('#filevoide').val(res.url);
				} 
				else
				{
					var domain = up.getOption('domain');
					url = domain + encodeURI(res.key);
					$('#filevoide').val(url);
				}
				
            },
            'Error': function(up, err, errTip) {
               alert('文件上传失败！');
            }
        }
    });

    uploader.bind('FileUploaded', function() {
        //console.log('hello man,a file is uploaded');
    });
    $('#container').on(
        'dragenter',
        function(e) {
            e.preventDefault();
            $('#container').addClass('draging');
            e.stopPropagation();
        }
    ).on('drop', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragleave', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragover', function(e) {
        e.preventDefault();
        $('#container').addClass('draging');
        e.stopPropagation();
    });
});
