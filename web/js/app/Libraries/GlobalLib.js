/**
 * Created by rockuo on 23.6.16.
 */

import Gateway from 'trinity/Gateway';
import Events from 'trinity/utils/Events';
/**
 *
 * @param route {string}
 * @param sourceField  {HTMLInputElement}
 * @param outputField {HTMLInputElement}
 */
export function slugify(route,sourceField, outputField) {
    Gateway.postJSON(route, {string: sourceField.value}, function (response) {
        outputField.value = response.body.handle;
    }, err => {
        console.error(err);
    });
}

export function handleHandleGeneration(route,titleField,handleField) {
    if (titleField && handleField) {
        return Events.listen(titleField, 'input', () => {
            slugify(route, titleField, handleField);
        });
    }else {
        return ()=>{};
    }
}