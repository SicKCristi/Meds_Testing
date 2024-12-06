document.addEventListener("DOMContentLoaded",()=>{
    const slides=document.querySelectorAll(".slide");

    const observer=new IntersectionObserver(
        (entries)=>{
            entries.forEach((entry)=>{
                if(entry.isIntersecting){
                    const slide=entry.target;

                    slide.classList.add("show");

                    observer.unobserve(slide);
                }
            });
        },
        {
            threshold: 0.2, // Setarea pragului de 20% vizibilitate pentru activarea tranziției
        }
    );

    slides.forEach((slide)=>{
        observer.observe(slide);
    });
});