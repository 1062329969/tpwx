

//评论
function comment(aid,type,token)
{
    var commentText=$('#commentText').val();
    if(commentText=='')
    {
        alert('评论内容不能为空哦~');
        return false;
    }

    $.ajax({
            type: "post",
            url: '/Comment/addComment.html',
            data: {text:commentText,aid:aid,type:type,__token__:token},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                window.location.reload();
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

    is=$(".spi3").attr('data-id');
    $.ajax({
            type: "post",
            url: '/praise/index.html',
            data: {is:is,aid:aid,type:type,__token__:token},
            dataType: "json",
            success: function(data)
            {
                if(data.code==1)
                {
                    $(".spi3 span").html(parseInt($(".spi3 span").html())+parseInt(1));
                    $(".spi3").attr('data-id',1);
                }
                else if(data.code==2)
                {
                    $(".spi3 span").html(parseInt($(".spi3 span").html())-parseInt(1));
                    $(".spi3").attr('data-id',0);
                }
                else
                {
                    alert(data.msg);
                }
            }
    });
}

//收藏
function collection(aid,type,token){
    is=$("#collection").attr('data-id');
    $.ajax({
        type: "post",
        url: '/collection/index.html',
        data: {is:is,aid:aid,type:type,__token__:token},
        dataType: "json",
        success: function(data)
        {
            if(data.code==1)
            {
                $("#collection").attr('data-id',1);
            }
            else if(data.code==2)
            {
                $("#collection").attr('data-id',0);
            }
            else
            {
                alert(data.msg);
            }
        }
    });
}


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
                $(".spi3").attr('data-id',0);
            }
            else if(data.code==2)
            {
                $(".spi3").attr('data-id',1);
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
}
function hidefxdiv(){
    $('#fxdiv').hide();
}