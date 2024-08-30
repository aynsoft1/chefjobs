var optionListCount=0;
var optionListObjects = new Array();
////////////////////////////////////////////////////////////////////
function initOptionLists()
{
	for(var i=0;i<optionListObjects.length;i++)
	{
		var dol = optionListObjects[i];
		if(dol.formName!=null)
		{
			dol.form = document.forms[dol.formName];
		}
		else if
		(
			dol.formIndex!=null)
			{
			dol.form = document.forms[dol.formIndex];
			}
			else
			{
				var name = dol.fieldNames[0][0];
				for(var f=0;f<document.forms.length;f++)
				{
					if(typeof(document.forms[f][name])!="undefined")
					{
						dol.form = document.forms[f];
						break;
					}
				}
				if(dol.form==null)
				{
					alert("ERROR: Couldn't find form element "+name+" in any form on the page! Init aborted");
					return;
				}
			}
			for(var j=0;j<dol.fieldNames.length;j++)
			{
				for(var k=0;k<dol.fieldNames[j].length-1;k++)
				{
					var selObj = dol.form[dol.fieldNames[j][k]];
					if(typeof(selObj)=="undefined")
					{
						alert("Select box named "+dol.fieldNames[j][k]+" could not be found in the form. Init aborted");
						return;
					}
					if(k==0)
					{
						if(selObj.options!=null)
						{
							for(l=0;l<selObj.options.length;l++)
							{
								var sopt = selObj.options[l];
								var m = dol.findMatchingOptionInArray(dol.options,sopt.text,sopt.value,false);
								if(m!=null)
								{
									var reselectForNN6 = sopt.selected;
									var m2 = new Option(sopt.text, sopt.value, sopt.defaultSelected, sopt.selected);
									m2.selected = sopt.selected;
									m2.defaultSelected = sopt.defaultSelected;
									m2.parentOption = m;
									selObj.options[l] = m2;
									selObj.options[l].selected = reselectForNN6;
								}
							}
						}
					}
					if(selObj.onchange==null)
					{
						selObj.onchange = new Function("optionListObjects["+dol.index+"].change(this)");
					}
				}
			}
		}
		resetoptionLists();
 }
///////////////////////////////////////////////////////////////////////////
function resetoptionLists(theform)
{
	for(var i=0;i<optionListObjects.length;i++)
	{
		var dol = optionListObjects[i];
		if(typeof(theform)=="undefined" || theform==null || theform==dol.form)
		{
			for(var j=0;j<dol.fieldNames.length;j++)
			{
				dol.change(dol.form[dol.fieldNames[j][0]],true);
			}
		}
	}
}
///////////////////////////////////////////////////////////////////////////
function parentOption(text,value,defaultSelected,selected)
{
	this.text = text;
	this.value = value;
	this.defaultSelected = defaultSelected;
	this.selected = selected;
	this.options = new Array();
	return this;
}
///////////////////////////////////////////////////////////////////////////
function optionList()
{
	this.form = null;
	this.options = new Array();
	this.longestString = new Array();
	this.numberOfOptions = new Array();
	this.currentNode = null;
	this.currentField = null;
	this.currentNodeDepth = 0;
	this.fieldNames = new Array();
	this.formIndex = null;
	this.formName = null;
	this.fieldListIndexes = new Object();
	this.fieldIndexes = new Object();
	this.selectFirstOption = true;
	this.numberOfOptions = new Array();
	this.longestString = new Array();
	this.values = new Object();
	this.forValue = parent_forValue;
	this.forText = parent_forText;
	this.forField = parent_forField;
	this.forX = parent_forX;
	this.addOptions = parent_addOptions;
	this.addOptionsTextValue = parent_addOptionsTextValue;
	this.setDefaultOptions = parent_setDefaultOptions;
	this.setValues = parent_setValues;
	this.setValue = parent_setValues;
	this.setFormIndex = parent_setFormIndex;
	this.setFormName = parent_setFormName;
	this.printOptions = parent_printOptions;
	this.addDependentFields = parent_addDependentFields;
	this.change = parent_change;
	this.child = parent_child;
	this.selectChildOptions = parent_selectChildOptions;
	this.populateChild = parent_populateChild;
	this.change = parent_change;
	this.addNewOptionToList = parent_addNewOptionToList;
	this.findMatchingOptionInArray = parent_findMatchingOptionInArray;
	if(arguments.length > 0)
	{
		for(var i=0;i<arguments.length;i++)
		{
			this.fieldListIndexes[arguments[i].toString()] = this.fieldNames.length;
			this.fieldIndexes[arguments[i].toString()] = i;
		}
		this.fieldNames[this.fieldNames.length] = arguments;
	}
	this.index = window.optionListCount++;
	window["optionListObjects"][this.index] = this;
}
///////////////////////////////////////////////////////////////////////////
function parent_findMatchingOptionInArray(a,text,value,exactMatchRequired)
{
	if(a==null || typeof(a)=="undefined")
	{
		return null;
	}
	var value_match = null;
	var text_match = null;
	for(var i=0;i<a.length;i++)
	{
		var opt = a[i];
		if(opt.value==value && opt.text==text)
		{
			return opt;
		}
		if(!exactMatchRequired)
		{
			if(value_match==null && value!=null && opt.value==value)
			{
				value_match = opt;
			}
			if(text_match==null && text!=null && opt.text==text)
			{
				text_match = opt;
			}
		}
	}
	return(value_match!=null)?value_match:text_match;
}
///////////////////////////////////////////////////////////////////////////
function parent_forX(s,type)
{
	if(this.currentNode==null)
	{
		this.currentNodeDepth=0;
	}
	var useNode =(this.currentNode==null)?this:this.currentNode;
	var o = this.findMatchingOptionInArray(useNode["options"],(type=="text")?s:null,(type=="value")?s:null,false);
	if(o==null)
	{
		o = new parentOption(null,null,false,false);
		o[type] = s;
	 useNode.options[useNode.options.length] = o;
 }
	this.currentNode = o;
	this.currentNodeDepth++;
	return this;
}

function parent_forValue(s)
{
	return this.forX(s,"value");
}
///////////////////////////////////////////////////////////////////////////
function parent_forText(s)
{
	return this.forX(s,"text");
}
///////////////////////////////////////////////////////////////////////////
function parent_forField(f)
{
	this.currentField = f;
	return this;
}
///////////////////////////////////////////////////////////////////////////
function parent_addNewOptionToList(a, text, value, defaultSelected)
{
	var o = new parentOption(text,value,defaultSelected,false);
	if(a==null)
	{
		a = new Array();
	}
	for(var i=0;i<a.length;i++)
	{
		if(a[i].text==o.text && a[i].value==o.value)
		{
			if(o.selected)
				{
				 a[i].selected=true;
			 }
				if(o.defaultSelected)
				{
					a[i].defaultSelected = true;
				}
				return a;
			}
		}
		a[a.length] = o;
	}
///////////////////////////////////////////////////////////////////////////
function parent_addOptions()
{
	if(this.currentNode==null)
	{
		this.currentNode = this;
	}
	if(this.currentNode["options"] == null)
	{
		this.currentNode["options"] = new Array();
	}
	for(var i=0;i<arguments.length;i++)
	{
		var text = arguments[i];
		this.addNewOptionToList(this.currentNode.options,text,text,false);
		if(typeof(this.numberOfOptions[this.currentNodeDepth])=="undefined")
		{
			this.numberOfOptions[this.currentNodeDepth]=0;
		}
		if(this.currentNode.options.length > this.numberOfOptions[this.currentNodeDepth])
		{
			this.numberOfOptions[this.currentNodeDepth] = this.currentNode.options.length;
		}
		if(typeof(this.longestString[this.currentNodeDepth])=="undefined" ||(text.length > this.longestString[this.currentNodeDepth].length))
		{
			this.longestString[this.currentNodeDepth] = text;
		}
	}
	this.currentNode = null;this.currentNodeDepth = 0;
}
///////////////////////////////////////////////////////////////////////////
function parent_addOptionsTextValue()
{
	if(this.currentNode==null)
	{
		this.currentNode = this;
	}
	if(this.currentNode["options"] == null)
	{
		this.currentNode["options"] = new Array();
	}
	for(var i=0;i<arguments.length;i++)
	{
		var text = arguments[i++];
		var value = arguments[i];
		this.addNewOptionToList(this.currentNode.options,text,value,false);
		if(typeof(this.numberOfOptions[this.currentNodeDepth])=="undefined")
		{
			this.numberOfOptions[this.currentNodeDepth]=0;
		}
		if(this.currentNode.options.length > this.numberOfOptions[this.currentNodeDepth])
		{
			this.numberOfOptions[this.currentNodeDepth] = this.currentNode.options.length;
		}
		if(typeof(this.longestString[this.currentNodeDepth])=="undefined" ||(text.length > this.longestString[this.currentNodeDepth].length))
		{
			this.longestString[this.currentNodeDepth] = text;
		}
	}
	this.currentNode = null;
	this.currentNodeDepth = 0;
}
///////////////////////////////////////////////////////////////////////////
function parent_child(obj)
{
	var listIndex = this.fieldListIndexes[obj.name];
	var index = this.fieldIndexes[obj.name];
	if(index <(this.fieldNames[listIndex].length-1))
	{
		return this.form[this.fieldNames[listIndex][index+1]];
	}
	return null;
}
///////////////////////////////////////////////////////////////////////////
function parent_setDefaultOptions()
{
	if(this.currentNode==null)
	{
		this.currentNode = this;
	}
	for(var i=0;i<arguments.length;i++)
	{
		var o = this.findMatchingOptionInArray(this.currentNode.options,null,arguments[i],false);
		if(o!=null)
		{
			o.defaultSelected = true;
		}
	}
	this.currentNode = null;
}
///////////////////////////////////////////////////////////////////////////
function parent_setValues()
{
	if(this.currentField==null)
	{
		alert("Can't call setValues() without using forField() first!");
		return;
	}
	if(typeof(this.values[this.currentField])=="undefined")
	{
		this.values[this.currentField] = new Object();
	}
	for(var i=0;i<arguments.length;i++)
	{
		this.values[this.currentField][arguments[i]] = true;
	}
	this.currentField = null;
}
///////////////////////////////////////////////////////////////////////////
function parent_setFormIndex(i)
{
	this.formIndex = i;
}
///////////////////////////////////////////////////////////////////////////
function parent_setFormName(n)
{
	this.formName = n;
}
///////////////////////////////////////////////////////////////////////////
function parent_printOptions(name)
{
	if((navigator.appName == 'Netscape') &&(parseInt(navigator.appVersion) <= 4))
	{
		var index = this.fieldIndexes[name];
		var ret = "";
		if(typeof(this.numberOfOptions[index])!="undefined")
		{
			for(var i=0;i<this.numberOfOptions[index];i++)
			{
				ret += "<OPTION>";
			}
		}
		ret += "<OPTION>";
		if(typeof(this.longestString[index])!="undefined")
		{
			for(var i=0;i<this.longestString[index].length;i++)
			{
				ret += "_";
			}
		}
		document.writeln(ret);
	}
}
///////////////////////////////////////////////////////////////////////////
function parent_addDependentFields()
{
	for(var i=0;i<arguments.length;i++)
	{
		this.fieldListIndexes[arguments[i].toString()] = this.fieldNames.length;
		this.fieldIndexes[arguments[i].toString()] = i;
	}
	this.fieldNames[this.fieldNames.length] = arguments;
}
///////////////////////////////////////////////////////////////////////////
function parent_change(obj, usePreselected)
{
	if(usePreselected==null || typeof(usePreselected)=="undefined")
	{
		usePreselected = false;
	}
	var changedListIndex = this.fieldListIndexes[obj.name];
	var changedIndex = this.fieldIndexes[obj.name];
	var child = this.child(obj);
	if(child == null)
	{
		return;
	}
	if(obj.type == "select-one")
	{
		if(child.options!=null)
		{
			child.options.length=0;
		}
		if(obj.options!=null && obj.options.length>0 && obj.selectedIndex>=0)
		{
			var o = obj.options[obj.selectedIndex];
			this.populateChild(o.parentOption,child,usePreselected);
			this.selectChildOptions(child,usePreselected);
		}
	}
	else if(obj.type == "select-multiple")
	{
		var currentlySelectedOptions = new Array();
		if(!usePreselected)
		{
			for(var i=0;i<child.options.length;i++)
			{
				var co = child.options[i];
				if(co.selected)
				{
					this.addNewOptionToList(currentlySelectedOptions, co.text, co.value, co.defaultSelected);
				}
			}
		}
		child.options.length=0;
		if(obj.options!=null)
		{
			var obj_o = obj.options;
			for(var i=0;i<obj_o.length;i++)
			{
				if(obj_o[i].selected)
				{
					this.populateChild(obj_o[i].parentOption,child,usePreselected);
				}
			}
			var atLeastOneSelected = false;
			if(!usePreselected)
			{
				for(var i=0;i<child.options.length;i++)
				{
					var m = this.findMatchingOptionInArray(currentlySelectedOptions,child.options[i].text,child.options[i].value,true);
					if(m!=null)
					{
						child.options[i].selected = true;
						atLeastOneSelected = true;
					}
				}
			}
			if(!atLeastOneSelected)
			{
				this.selectChildOptions(child,usePreselected);
			}
		}
	}
	this.change(child,usePreselected);
}
///////////////////////////////////////////////////////////////////////////
function parent_populateChild(dolOption,childSelectObj,usePreselected)
{
	if(dolOption!=null && dolOption.options!=null)
	{
		for(var j=0;j<dolOption.options.length;j++)
		{
			var srcOpt = dolOption.options[j];
			if(childSelectObj.options==null)
			{
				childSelectObj.options = new Array();
			}
			var duplicate = false;
			var preSelectedExists = false;
			for(var k=0;k<childSelectObj.options.length;k++)
			{
				var csi = childSelectObj.options[k];
				if(csi.text==srcOpt.text && csi.value==srcOpt.value)
				{
					duplicate = true;
					break;
				}
			}
			if(!duplicate)
			{
				var newopt = new Option(srcOpt.text, srcOpt.value, false, false);
				newopt.selected = false;
				newopt.defaultSelected = false;
				newopt.parentOption = srcOpt;
				childSelectObj.options[childSelectObj.options.length] = newopt;
			}
		}
	}
}
///////////////////////////////////////////////////////////////////////////
function parent_selectChildOptions(obj,usePreselected)
{
	var values = this.values[obj.name];
	var preselectedExists = false;
	if(usePreselected && values!=null && typeof(values)!="undefined")
	{
		for(var i=0;i<obj.options.length;i++)
		{
			var v = obj.options[i].value;
			if(v!=null && values[v]!=null && typeof(values[v])!="undefined")
			{
				preselectedExists = true;break;
			}
		}
	}
	var atLeastOneSelected = false;
	for(var i=0;i<obj.options.length;i++)
	{
		var o = obj.options[i];
		if(preselectedExists && o.value!=null && values[o.value]!=null && typeof(values[o.value])!="undefined")
		{
			o.selected = true;
			atLeastOneSelected = true;
		}
		else if(!preselectedExists && o.parentOption!=null && o.parentOption.defaultSelected)
		{
			o.selected = true;
			atLeastOneSelected = true;
		}
		else
		{
			o.selected = false;
		}
	}
	if(this.selectFirstOption && !atLeastOneSelected && obj.options.length>0)
	{
		obj.options[0].selected = true;
	}
	else if(!atLeastOneSelected &&  obj.type=="select-one")
	{
		obj.selectedIndex = -1;
	}
}
///////////////////////////////////////////////////////////////////////////