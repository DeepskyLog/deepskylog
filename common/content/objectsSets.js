function generate()
{ i=0;
  while(theobject=document.getElementById('R'+i))
  { var load = window.open('objectsSet.pdf?theobject='+document.getElementById('R'+i).innerHTML+
                                          '&theSet='+document.getElementById('R'+i+'Dfov').value+
                                          '&thedsos='+document.getElementById('R'+i+'Ddsos').value+
                                          '&thestars='+document.getElementById('R'+i+'Dstars').value,
                           document.getElementById('R'+i).innerHTML
                           );
    alert('Click "Ok", only when '+document.getElementById('R'+i).innerHTML+' is finished!');
    i++;
  }
}