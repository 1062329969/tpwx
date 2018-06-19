

//评论
function comment(aid,type,token,tid)
{
    var commentText=$('#commentText').val();
    var type=type;
    if(commentText=='')
    {
        alert('评论内容不能为空哦~');
        return false;
    }

    $.ajax({
            type: "post",
            url: '/Comment/addComment.html',
            data: {text:commentText,aid:aid,tid:tid,type:type,__token__:token},
            dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                //如果是视频评论就跳转到详细页
                if(type=='7')
                {
                    window.location.href="/video/comment/id/"+aid+".html";
                }
                else
                {
                    window.location.reload();
                }

            }
            else if(data.code==-1)
            {
                window.location.href='/member/login.html';
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}

//回复评论
function recComment(aid,type,token,plid,uid,text,uuid)
{

    $.ajax({
        type: "post",
        url: '/Comment/recComment.html',
        data: {text:text,aid:aid,type:type,plid:plid,reuid:uid,uuid:uuid,__token__:token},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                window.location.reload();
            }
            else if(data.code==-1)
            {
                window.location.href='/member/login.html';
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}


//点赞
function praise(aid,type,token){
    if($(".dianzan").attr('dis')==1){
        return false;
    }
    $(".dianzan").attr('dis',1);
    is=$(".dianzan").attr('data-id');
    var opennum=1; //防止重复提交
    if($(".dianzan").attr('dis')==1)
    {
        opennum=0;
        $.ajax({
            type: "post",
            url: '/praise/index.html',
            data: {is:is,aid:aid,type:type,__token__:token},
            dataType: "json",
            success: function(data)
            {
                if(data.code==1)
                {
                    $(".dianzan span").html(parseInt($(".dianzan span").html())+parseInt(1));
                    $(".dianzan").attr('data-id',1);
                    $(".dianzan img").attr('src','/static/index/images/66.png');
                    $(".dianzan").css('color','#ff3e47');
                    $(".dianzan").attr('dis',0);
                }
                else if(data.code==2)
                {
                    $(".dianzan span").html(parseInt($(".dianzan span").html())-parseInt(1));
                    $(".dianzan").attr('data-id',0);
                    $(".dianzan img").attr('src','/static/index/images/65.png');
                    $(".dianzan").css('color','#999');
                    $(".dianzan").attr('dis',0);
                    //alert('您已经点过赞了哦~~');
                }
                else
                {
                    alert(data.msg);
                }
                opennum=1;

            }
        });

    }

}



//首页点赞
function praise3(obj,type,token){
    var $this = obj;
    var aid = $($this).attr("data2_id");
    if($($this).attr('dis')==1){
        return false;
    }
    
    $($this).attr('dis',1);
    is=$($this).attr('data-id');
    var opennum=1; //防止重复提交
    if($($this).attr('dis')==1)

    $($this).attr('dis',1);
    is=$($this).attr('data-id');
    var opennum=1; //防止重复提交
    if($($this).attr('dis')==1)

    {
        opennum=0;
        $.ajax({
            type: "post",
            url: '/praise/index.html',
            data: {is:is,aid:aid,type:type,__token__:token},
            dataType: "json",
            success: function(data)
            {
                if(data.code==1)
                {

                    $($this).next().html(parseInt($($this).next().html())+parseInt(1));
                    $($this).attr('data-id',1);
                    $($this).attr('src','/static/index/images/66.png');
                    $($this).css('color','#ff3e47');
                    $($this).attr('dis',0);

                }
                else if(data.code==2)
                {

                    $($this).next().html(parseInt($($this).next().html())-parseInt(1));
                    $($this).attr('data-id',0);
                    $($this).attr('src','/static/index/images/65.png');
                    $($this).css('color','#999');
                    $($this).attr('dis',0);
                    //alert('您已经点过赞了哦~~');
                }
                else
                {
                    alert(data.msg);
                }
                opennum=1;

            }
        });

    }

}

//鄙视

function praise2(aid,type,token){
    is=$("dianzan2").attr('data-id');
    var opennum=1; //防止重复提交
    if(opennum==1) {
        opennum = 0;
        $.ajax({
            type: "post",
            url: '/praise/index2.html',
            data: {is: is, aid: aid, type: type, __token__: token},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    $(".dianzan2 span").html(parseInt($(".dianzan span").html()) + parseInt(1));
                    $(".dianzan2").attr('data-id', 1);
                }
                else if (data.code == 2) {
                    //$(".dianzan span").html(parseInt($(".dianzan span").html())-parseInt(1));
                    //$(".dianzan").attr('data-id',0);
                    alert('您已经点过了哦~~');
                }
                else {
                    alert(data.msg);
                }
                opennum =1;
            }
        });

    }
}

//评论点赞
function plpraise(plid,type,ppuid,pdata,token,hc,divid,lxuid){
    var opennum=1; //防止重复提交
    if(opennum==1) {
        opennum = 0;
        $.ajax({
            type: "post",
            url: '/plpraise/index.html',
            data: {id: plid, type: type,ppuid: ppuid,pdata:pdata, __token__: token,hc:hc,lxuid:lxuid},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) + parseInt(1));
                    $('#' + divid + " img").attr('src', '/static/index/images/66.png');
                    $('#' + divid + " span").css('color', '#ff3e47');
                    //$(".txdz").attr('data-id',1);
                }
                else if (data.code == 2) {
                    $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) - parseInt(1));
                    $('#' + divid + " img").attr('src', '/static/index/images/65.png');
                    $('#' + divid + " span").css('color', '#888b94');
                    //alert('您已经点过赞了哦~~');
                }
                else {
                    alert(data.msg);
                }
                opennum =1;
            }
        });

    }
}


//问答回答点赞
function askpraise(plid,type,token,divid,isxz){
    var opennum=1; //防止重复提交
    if(opennum==1) {
        opennum = 0;
        $.ajax({
            type: "post",
            url: '/plpraise/index.html',
            data: {id: plid, type: type, isxz: isxz, __token__: token},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    if (isxz == 0) {
                        $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) + parseInt(1));
                        $('#' + divid + " img").attr('src', '/static/index/images/dz-2.png');
                        $('#' + divid + " span").css('color', '#ff3e47');
                        //$(".txdz").attr('data-id',1);
                    }
                    else if (isxz == 1) {
                        $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) + parseInt(1));
                        $('#' + divid + " img").attr('src', '/static/index/images/bishi2.png');
                        $('#' + divid + " span").css('color', '#ff3e47');
                    }

                }
                else if (data.code == 2) {
                    if (isxz == 0) {
                        $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) - parseInt(1));
                        $('#' + divid + " img").attr('src', '/static/index/images/askdz.png');
                        $('#' + divid + " span").css('color', '#888b94');
                    }
                    else if (isxz == 1) {
                        $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) - parseInt(1));
                        $('#' + divid + " img").attr('src', '/static/index/images/bishi.png');
                        $('#' + divid + " span").css('color', '#888b94');
                    }
                    //alert('您已经点过赞了哦~~');
                }
                else {
                    alert(data.msg);
                }
                opennum =1;
            }
        });

    }
}


//问答首页点赞
function askindexpraise(id,type,token,divid){

    if($(".dianzan").attr('dis')==1){
        return false;
    }
    $(".dianzan").attr('dis',1);
    is=$(".dianzan").attr('data2-id');
    var opennum=1; //防止重复提交
    if(opennum==1) {
        opennum = 0;+
        $.ajax({
            type: "post",
            url: '/praise/index.html',
            data: {is:is,aid:id,type:type, __token__: token},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    $(".dianzan").attr('data2-id', 1);
                    $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) + parseInt(1));
                    $('#' + divid + " img").attr('src', '/static/index/images/66.png');
                    $('#' + divid + " span").css('color', '#ff3e47');
                    $(".dianzan").attr('dis',0);
                }
                else if (data.code == 2) {
                    $(".dianzan").attr('data2-id', 0);
                    $('#' + divid + " span").html(parseInt($('#' + divid + " span").html()) - parseInt(1));
                    $('#' + divid + " img").attr('src', '/static/index/images/65.png');
                    $('#' + divid + " span").css('color', '#888b94');
                    $(".dianzan").attr('dis',0);
                }
                else {
                    alert(data.msg);
                }
                opennum =1;
            }
        });

    }
}

//收藏
function collection(id,type,token){
    var $this=$("#collection");
    var aid=$($this).attr("data-id");
    if($("#collection").attr('dis')==1){
        return false;
    }

    $("#collection").attr("dis",1)
    is=$("#collection").attr('data-id');
    var opennum=1; //防止重复提交
    if($($this).attr("dis")==1)
        $($this).attr("dis",1)
    is=$($this).attr("data-id")
    var opennum=1;
    if($($this).attr('dis')==1) {
        opennum=0;
        $.ajax({
            type: "post",
            url: '/collection/index.html',
            data: {is: is, aid:id, type: type, __token__: token},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    $("#collection").attr('data-id', 1);
                    $("#collection span").html(parseInt($("#collection span").html()) + parseInt(1));
                    $("#ky img").attr('src', '/static/index/images/64.png');
                    $("#fbsc").attr('src', '/static/index/images/64.png');
                    $("#collection").css('color', '#999');
                    $("#collection").attr('dis',0);
                    console.log( $("#collection").attr("dis"))
                    //alert('收藏成功');
                }
                else if (data.code == -1) {
                    window.location.href = '/member/login.html';
                }
                else if (data.code == 2) {
                    $("#collection").attr('data-id', 0);
                    $("#collection span").html(parseInt($("#collection span").html()) - parseInt(1));
                    $("#collection img").attr('src', '/static/index/images/63.png');
                    $("#fbsc").attr('src', '/static/index/images/63.png');
                    $("#collection").css('color', '#999');
                    $("#collection").attr('dis',0);
                    //alert('您已经已经收藏了哦~');
                }
                else {
                    alert(data.msg);
                }
                opennum =1;

            }
        });

    }
}


// //取消收藏
//
// function collection2(id,type,token){
//     if($("#collection2").attr('dis')==0){
//         return false;
//     }
//     $("#collection2").attr('dis',1);
//     is=$("#collection2").attr('data-id');
//     $.ajax({
//         type: "post",
//         url: '/collection/index.html',
//         data: {is: is, aid:id, type: type, __token__: token},
//         dataType: "json",
//         success: function(data)
//         {
//             if(data.code==1)
//             {
//                 $("#collection").attr('data-id', 1);
//                 $("#collection span").html(parseInt($("#collection span").html()) - parseInt(1));
//                 $("#collection img").attr('src', '/static/index/images/63.png');
//                 $("#fbsc").attr('src', '/static/index/images/63.png');
//                 $("#collection").css('color', '#999');
//                 $("#collection").attr('dis',0);
//                 //alert('您已经已经收藏了哦~');
//             }
//             else if(data.code==2)
//             {
//                 $("#collection2").attr('data-id', 0);
//                 $("#collection2 span").html(parseInt($("#collection2 span").html()) + parseInt(1));
//                 $("#collection2 img").attr('src', '/static/index/images/63.png');
//                 $("#fbsc1").attr('src', '/static/index/images/63.png');
//                 $("#collection2").css('color', '#999');
//                 $("#collection2").attr('dis',0);
//                 //alert('您已经已经收藏了哦~');
//             }
//             else
//             {
//                 alert(data.msg);
//             }
//         }
//     });
// }
//
//



//是否收藏
function getCollection(aid,uid,type)
{
    $.ajax({
        type: "post",
        url: '/collection/getCollection.html',
        data: {aid:aid,uid:uid,type:type},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                $("#collection").attr('data-id',0);
            }
            else if(data.code==2)
            {
                $("#collection").attr('data-id',1);
                $("#collection img").attr('src','/static/index/images/xxsc-2.png');
                $("#fbsc").attr('src','/static/index/images/xxsc-2.png');
                $("#collection").css('color','#ff3e47');
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}

//是否点赞
function getPraise(aid,uid,type)
{
    $.ajax({
        type: "post",
        url: '/praise/getPraise.html',
        data: {aid:aid,uid:uid,type:type},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                $(".dianzan").attr('data-id',0);
            }
            else if(data.code==2)
            {
                $(".dianzan").attr('data-id',1);
                $(".ds1 img").attr('src','/static/index/images/66.png');
                $(".ds1").css('color','#ff3e47');
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}

//是否鄙视
function getPraise2(aid,uid,type)
{
    $.ajax({
        type: "post",
        url: '/praise/getPraise2.html',
        data: {aid:aid,uid:uid,type:type},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                $(".dianzan2").attr('data-id',0);
            }
            else if(data.code==2)
            {
                $(".dianzan2").attr('data-id',1);
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}



function fenxiang(){
    $('#fxdiv').show();
    CreatNodeAll();
}
function hidefxdiv(){
    $('#fxdiv').hide();
    window.location.reload();
}

