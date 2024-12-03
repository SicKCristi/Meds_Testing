document.addEventListener("DOMContentLoaded",function(){
    // Funcția prin care este gestionată activarea butonului de ștergere
    document.body.addEventListener("change",function(e){
        if (e.target.classList.contains("radio-select")){
            // Găsește butonul din formularul curent și îl activează
            const form=e.target.closest("form");
            if(form){
                const deleteButton=form.querySelector(".btn-danger");
                if(deleteButton){
                    deleteButton.disabled=false;
                    deleteButton.classList.remove("btn-disabled");
                }
            }
        }
    });

    // Funcția care asigură resetarea stării butonului când modal-ul este închis
    document.querySelectorAll(".modal").forEach((modal)=>{
        modal.addEventListener("hidden.bs.modal",function(){
            const forms=modal.querySelectorAll("form");
            forms.forEach((form)=>{
                const deleteButton=form.querySelector(".btn-danger");
                if (deleteButton){
                    deleteButton.disabled=true;
                    deleteButton.classList.add("btn-disabled");
                }
                form.reset();
            });
        });
    });
});
