function addField() {
    let modal_body_create = document.querySelector('#modal_body_create');

    let newDiv = document.createElement('div');
    newDiv.classList.add('mb-3');

    let options = languages.map(category => {
        return `<option value="${category.id}">${category.language}</option>`;
    }).join('');

    newDiv.innerHTML = `
    <hr style='margin:0;'>
    <hr style='margin:0 0 1rem 0;'>
        <div class="mb-3">
            <label for="category_id" class="form-label">Category Name</label>
            <select class="form-select" name="language_id[]" id="category_id" aria-label="Default select example" required>
                <option disabled selected>Select Parent Category</option>
                ${options}
            </select>
        </div>
        <label for="category" class="form-label">Verse</label>
        <input type="text" name="verse[]" class="form-control" id="verse" placeholder="Enter Title" required>
        <button class="btn btn-danger my-2" type="button" onClick="remove(this)">Remove Field</button>
    `;

    modal_body_create.appendChild(newDiv);
}

function remove(a){
    console.log(a.parentNode.parentNode);
    fieldToRemove = a.parentNode;
    fieldToRemove.remove();
}
