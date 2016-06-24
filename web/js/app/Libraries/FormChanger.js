/**
 * Created by Jakub on 09.02.16.
 */

import Gateway from 'trinity/Gateway';

// Used for switching similar forms and copying it's data from form to form
export default class FormChanger {
    /**
     * Call the "url" to get a form which will be inserted into "parentElement".
     * After inserting the form the data from old form are copied to the new form.
     * The "afterFormChange" callback is called after the process.
     *
     * @param {Element}  parentElement    Element which will contain the form which will ge get from url
     * @param {string}   url              Url which will be used to get the form
     * @param {function} afterFormChange  Function which is called after the refreshing is done
     */
    static refreshForm(parentElement, url, afterFormChange) {
        //call api and get form
        Gateway.get(url, {}, function (response) {
            //save data from old form
            let oldHtml = parentElement.innerHTML;

            // If the element already contains a form
            // Change the form to the new form and copy the data
            if (oldHtml.includes("<form")) {
                let oldForm = parentElement.getElementsByTagName("form")[0];

                parentElement.innerHTML = response.text;
                let newForm = parentElement.getElementsByTagName("form")[0];

                //copy data to new form
                FormChanger.copyFormData(oldForm, newForm);

            } else {
                parentElement.innerHTML = response.text;
            }
            if (typeof afterFormChange === 'function') {
                afterFormChange();
            }
        });
    }

    static copyFormData(oldForm, newForm) {
        let oldInputs = FormChanger.getFormInputs(oldForm);
        let newInputs = FormChanger.getFormInputs(newForm);

        for (let newFormInput of newInputs) {
            let newInputName = FormChanger.getShortInputName(newFormInput);

            for (let oldInput of oldInputs) {
                //if the input names are the same
                if (newInputName !== "[_token]" && newInputName === FormChanger.getShortInputName(oldInput)) {
                    if('checkbox' === newFormInput.getAttribute('type')) {
                        newFormInput.checked = oldInput.checked;
                    } else {
                        newFormInput.value = oldInput.value;
                    }
                }
            }
        }
    }

    static getShortInputName(formInput) {
        // e.g. free_product[name]
        let formInputFullName = formInput.getAttribute('name');

        let leftSquareBracketIndex = formInputFullName.indexOf('[');
        let rightSquareBracketIndex = formInputFullName.indexOf(']');

        // e.g name
        return formInputFullName.substr(leftSquareBracketIndex, rightSquareBracketIndex);
    }

    static getFormInputs(form) {
        let inputs = Array.prototype.slice.call(form.getElementsByTagName("input"));
        let textAreas = Array.prototype.slice.call(form.getElementsByTagName("textarea"));
        let formName = form.getAttribute('name');
        let formTextAreas = [];
        let formInputs = [];

        inputs.forEach(function(input){
            //check if the input contains id of the form
            if(input.getAttribute("id") && -1 !== input.getAttribute("id").indexOf(formName)) {
                formInputs.push(input);
            }
        });

        textAreas.forEach(function(input){
            //check if the input contains id of the form
            if(input.getAttribute("id") && -1 !== input.getAttribute("id").indexOf(formName)) {
                formTextAreas.push(input);
            }
        });

        //convert node list of textareas and push it to the end of inputs
        return formInputs.concat(formTextAreas);
    }
}



