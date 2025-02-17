//alert("cargado");
let fi = document.getElementById("fichero");

fi.oninput = function()
{
    if(fi.files.length > 5)
    {
        document.getElementById("archivos").innerHTML = "No se enviaran archivos, Limite alcanzado (5)";
    }
    else
    {
        for(var i=0; i < fi.files.length; i++)
        {
            document.getElementById("archivos").innerHTML += `<p> ${fi.files.item(i).name} </p>`;
        }   
    }
} 


function cancelaArchivos(){
    archivosCargados = 0;
    
    document.getElementById("fichero").value = "";
    document.getElementById("archivos").innerHTML = "";
}

function refresca(){
    location.reload();

}

function clean(){
    let titletxt = document.getElementById("txt1");
    let contentxt = document.getElementById("txt2");
    titletxt.value = " ";
    contentxt.value = " ";
}


/*
var selDiv = "";
document.addEventListener("DOMContentLoaded", init, false);
	
function init() {
    document.querySelector('#fichero').addEventListener('change', handleFileSelect, false);
    selDiv = document.querySelector('#archivos');
}
		
function handleFileSelect(e) {
    
    if(!e.target.files) return;
    
    selDiv.innerHTML = "";
    
    var files = e.target.files;
    for(var i=0; i<files.length; i++) {
        var f = files[i];
        
        selDiv.innerHTML += f.name + "<br/>";

    }
    
}
    */
	

//que suba hasta 5 archivos 
//si llega al limite que pare ahi
//cuando cancela reinicia todos los archivos y el contador