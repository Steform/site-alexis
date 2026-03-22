/**
 * Stub module: photo reorder uses native HTML5 drag-and-drop (see photo_reorder_controller.js).
 * Present so importmap entries named "sortablejs" resolve without importmap:install.
 *
 * If you switch to SortableJS, replace this file with the package ESM build from jsdelivr/npm.
 */
export default class SortableStub {
    static create() {
        return {};
    }
}
