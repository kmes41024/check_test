window.addEventListener( "load", loadName, false );

 		var list;

function loadName()
{
	$.ajax({  
		type: "POST",   //提交的方法
		datatype:"json",
		url:"searchList.php", //提交的地址  
		data:$('#searchForm').serialize(),// 序列化表单值  
		success: function(data) {  //成功
			console.log(data.state);  //就将返回的数据显示出来
			if (data.state == 'success')
			{
				list = JSON.parse(data.return);
				console.log(list);
				
				var nameStr = "<option value = 'null'>[请选择姓名]</option>";
				for(var i = 0; i < list.length; i++)
				{
					nameStr = nameStr + "<option value = '" + list[i]['f_fullname'] + "'>" + list[i]['f_fullname'] + "</option>";
				}
				
				document.getElementById('name').innerHTML = nameStr;
				document.getElementById('market').value = "";
				document.getElementById('company').value = "";
				document.getElementById('mobile').value = "";
			}
			else
			{
				layui.layer.alert('传送失败, 请确认内容后,重新发送一次');
			}
		},
	});
}

function changeInfo()
{
	if(document.getElementById('name').value != 'null')
	{
		for(var i = 0; i < list.length; i++)
		{
			if(list[i]['f_fullname'] == document.getElementById('name').value)
			{
				document.getElementById('market').value = list[i]['f_market_name'];
				document.getElementById('company').value = list[i]['f_company_name'];
				document.getElementById('mobile').value = list[i]['f_mobile'];
			}  
		}
	}
}

function systemOpen()
{
	var marketName;
	var companyID;
	var userID;
	for(var i = 0; i < list.length; i++)
	{
		if(document.getElementById('name').value == list[i]['f_fullname'])
		{
			var market;
			if(list[i]['f_market_name'] == 'HR市场')
			{
				market = 'HR';
			}
			else if(list[i]['f_market_name'] == '亲子市场')
			{
				market = 'Family';
			}
			else if(list[i]['f_market_name'] == '一般市场')
			{
				market = 'Normal';
			}
			
			marketName = market;
			companyID = list[i]['f_company_id'];
			userID = list[i]['f_user_id'];
		}
	}
	
	$.ajax({  
		type: "POST",   //提交的方法
		datatype:"json",
		url:"filyCopy.php", //提交的地址  
		data:
		{
			marketName:marketName,
			companyID:companyID,
			userID:userID
		},
		success: function(data) {  //成功
			console.log(data.state);  //就将返回的数据显示出来
			if (data.state == 'success')
			{
				layui.layer.msg('启动成功',{time:5000});
				setTimeout(function(){$('#contactwModal').modal('hide');}, 5000);
			}
			else
			{
				layui.layer.alert('传送失败, 请确认内容后,重新发送一次');
			}
		},
		complete: function(data) {  //成功
			console.log(data);  //就将返回的数据显示出来
		}
		
	});
}