document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.slider');

    const leftArrow = document.querySelector('.left');
    const rightArrow = document.querySelector('.right');
    const indicatorParents = document.querySelector('.controls ul');

    let sectionIndex = 0;
    var totalImages = slider.children.length;

    function doChange() {
        document.querySelector('.controls .selected').classList.remove('selected');
        slider.style.transform = 'translate(' + (sectionIndex) * -(100/totalImages) + '%)';
        indicatorParents.children[sectionIndex].classList.add('selected');
    }

    document.querySelectorAll('.controls li').forEach(function(indicator, ind) {
        indicator.addEventListener('click', function() {
            if (totalImages > 1) {
                console.log(totalImages);
                sectionIndex = ind;
                document.querySelector('.controls .selected').classList.remove('selected');
                slider.style.transform = 'translate(' + (sectionIndex) * -(100/totalImages) + '%)';
                indicator.classList.add('selected');
            }
        });
    });

    rightArrow.addEventListener('click', function() {
        if (totalImages > 1) {
            console.log(totalImages);
            sectionIndex = (sectionIndex < totalImages - 1) ? sectionIndex + 1 : totalImages - 1;
            doChange();
        }
    });

    leftArrow.addEventListener('click', function() {
        if (totalImages > 1) {
            console.log(totalImages);
            sectionIndex = (sectionIndex > 0) ? sectionIndex - 1 : 0;
            doChange();
        }
    });
});