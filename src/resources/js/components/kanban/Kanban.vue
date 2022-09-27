<template>
    <div v-if="kanban !== null">
        <kanban-bar :kanbanId="kanban.id"
                    :kanbanMembers="kanban.members"
                    :kanbanName="kanban.name"
                    :loadingMembers="loadingMembers"></kanban-bar>

        <div class="border border-gray-400 py-2 px-4 my-2 mx-10">
            <div class="flex space-x-4 items-center py-2">
                <label class="flex items-start cursor-pointer" for="mode">
                    <div class="mr-3 text-gray-700 font-medium text-right">
                        <p>Directory Mode</p>
                    </div>
                    <div class="relative mt-1">
                        <input class="hidden"
                               id="mode"
                               type="checkbox"
                               v-model="directoryData.mode ==='sequential'"
                               @change="setMode"/>
                        <div
                            class="toggle__dot absolute w-5 h-5 bg-blue-700 rounded-full shadow inset-y-0 left-0"></div>
                        <div class="toggle__line w-10 h-4 bg-gray-400 rounded-full shadow-inner"></div>
                    </div>
                    <div class="ml-3 text-gray-700 font-medium">
                        <p>Sequential Mode</p>
                    </div>
                </label>
                <small v-if="directoryData.mode==='sequential'" class="flex-1 text-green-700 font-semibold">Each employee will
                    be called in
                    sequential order until someone picks up.</small>
                <small v-else class="flex-1 text-blue-700 font-semibold">An intro message will play and then the caller
                    will hear a list of each employee name with the ability to choose who they want to talk to. </small>

                <button v-if="directoryData.mode==='sequential' && kanban.mode !== 'sequential'"
                        @click="saveModeChanges"
                        type="button"
                        class="border border-indigo-500 bg-indigo-500 text-white rounded-md px-4 py-1 transition duration-500 ease hover:bg-indigo-600 focus:outline-none focus:shadow-outline">
                    Save Mode Change
                </button>

            </div>
            <div v-if="directoryData.mode==='directory'">
                <hr class="mt-2 pt-2">

                <h3 class="font-bold">Your Intro Message</h3>

                <div class="w-full grid sm:grid-cols-2 gap-3 sm:gap-3">
                    <div class="pt-4">
                        <label for="englishIntro"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">English</label>
                        <textarea v-model="directoryData.messageEN"
                                  id="englishIntro" rows="4"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300"
                                  placeholder="Welcome to XGuard Security..."></textarea>

                        <div class="flex bg-blue-50 rounded-lg p-4 my-3 text-sm text-blue-800" role="alert">
                            <div>
                                <div class="font-medium">English Example</div>
                                <p class="pt-1">{{directoryData.messageEN}}</p>
                                <p>To speak to ***, press 1. To speak to *** press 2...</p>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4">
                        <label for="frenchIntro"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">French</label>
                        <textarea v-model="directoryData.messageFR"
                                  id="frenchIntro" rows="4"
                                  class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300"
                                  placeholder="Bienvenue chez Sécurité XGuard..."></textarea>

                        <div class="flex bg-blue-50 rounded-lg p-4 my-3 text-sm text-blue-800" role="alert">
                            <div>
                                <div class="font-medium">French Example</div>
                                <p class="pt-1">{{directoryData.messageFR}}</p>
                                <p class="pt-1">Pour parler à ***, appuyez sur le 1. Pour parler à ***, appuyez sur le 2... </p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="mt-2 pt-2">

                <div class="w-50 grid sm:grid-cols-2 gap-3 sm:gap-3">
                    <button @click="undoModeChanges"
                            type="button"
                            class="py-2 border border-gray-200 rounded text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-600 transition duration-300 ease-in-out">
                        Undo
                    </button>
                    <button @click="saveModeChanges"
                            type="button"
                            class="py-2 border border-transparent rounded text-white bg-indigo-600 hover:bg-indigo-500 transition duration-300 ease-in-out">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <div :key="row.id" class="mx-10 my-3" v-for="(row, rowIndex) in kanban.rows">

            <div class="border bg-gray-700 pl-3 pr-3 rounded py-2 flex justify-between"
                 v-if="loadingColumn.rowId === row.id && loadingColumn.isLoading ">
                <h2 class="text-gray-100 font-medium tracking-wide animate-pulse">
                    Loading... </h2>
            </div>
            <div class="border bg-gray-700 pl-3 pr-3 rounded py-2 flex justify-between" v-else>
                <h2 class="text-gray-100 font-medium tracking-wide">
                    {{ row.name }} </h2>

                <a @click="createColumns(rowIndex, row.columns, row.id)"
                   class="px-2 text-gray-500 hover:text-gray-400 transition duration-300 ease-in-out focus:outline-none"
                   href="#">
                    <i class="fas fa-business-time"></i>
                </a>
            </div>
            <div class="flex flex-wrap">
                <div class="space-x-2  flex flex-1 pt-3 pb-2 overflow-x-auto overflow-y-hidden">
                    <div :key="column.id"
                         class="flex-1 bg-gray-200 px-3 py-3 column-width rounded"
                         v-for="(column, columnIndex) in row.columns">
                        <div class="flex" v-if="loadingCards.columnId === column.id && loadingCards.isLoading ">
                            <p class="flex-auto text-gray-700 font-semibold font-sans tracking-wide pt-1 animate-pulse">
                                Loading... </p>
                        </div>
                        <div class="flex" v-else>

                            <p class="flex-auto text-gray-700 font-semibold font-sans tracking-wide pt-1">
                                {{ column.name }} </p>

                            <button @click="createEmployeeCard(rowIndex, columnIndex)"
                                    class="w-6 h-6 bg-blue-200 rounded-full hover:bg-blue-300 mouse transition ease-in duration-200 focus:outline-none">
                                <i class="fas fa-plus text-white"></i>
                            </button>
                        </div>
                        <draggable :animation="200"
                                   :list="column.employee_cards"
                                   :disabled="isDraggableDisabled"
                                   @change="getChangeData($event, columnIndex, rowIndex)"
                                   class="h-full list-group"
                                   ghost-class="ghost-card"
                                   group="employees">
                            <employee-card :employee_card="employee_card"
                                           :key="employee_card.id"
                                           class="mt-3 cursor-move"
                                           :class="{'opacity-60':isDraggableDisabled}"
                                           v-for="employee_card in column.employee_cards"
                                           v-on:click.native="updateTask(employee_card.id)"></employee-card>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-5"/>

        <add-employee-card-modal :kanbanData="kanban"></add-employee-card-modal>
        <add-member-modal :kanbanData="kanban"></add-member-modal>
        <add-column-modal :kanbanData="kanban"></add-column-modal>
    </div>
</template>

<script>
import draggable from "vuedraggable";
import EmployeeCard from "./kanbanComponents/EmployeeCard.vue";
import AddEmployeeCardModal from "./kanbanComponents/AddEmployeeCardModal.vue";
import AddMemberModal from "./kanbanComponents/AddMemberModal.vue";
import AddColumnModal from "./kanbanComponents/AddColumnModal.vue";
import KanbanBar from "./kanbanComponents/KanbanBar.vue";
import {ajaxCalls} from "../../mixins/ajaxCallsMixin";

export default {
    inject: ["eventHub"],
    components: {
        EmployeeCard,
        draggable,
        AddEmployeeCardModal,
        AddMemberModal,
        AddColumnModal,
        KanbanBar,
    },

    mixins: [ajaxCalls],

    props: {'id': Number},

    data() {
        return {
            kanban: null,
            loadingColumn: {rowId: null, isLoading: false},
            loadingCards: {columnId: null, isLoading: false},
            loadingMembers: {memberId: null, isLoading: false},
            isDraggableDisabled: false,
            directoryData: {
                kanbanId: null,
                mode: null,
                messageEN: null,
                messageFR: null,
            }
        };
    },

    mounted() {
        this.getKanban(this.id);
    },

    watch: {
        id: function (newVal) {
            this.getKanban(newVal);
        }
    },

    created() {
        this.eventHub.$on("save-employee-cards", (cardData) => {
            this.saveEmployeeCards(cardData);
        });
        this.eventHub.$on("delete-employee-cards", (cardData) => {
            this.deleteEmployeeCard(cardData);
        });
        this.eventHub.$on("save-members", (selectedMembers) => {
            this.saveMember(selectedMembers);
        });
        this.eventHub.$on("remove-member", (memberData) => {
            this.deleteMember(memberData);
        });
        this.eventHub.$on("save-columns", (columnData) => {
            this.saveColumns(columnData);
        });
    },

    beforeDestroy() {
        this.eventHub.$off('save-employee-cards');
        this.eventHub.$off('delete-employee-cards');
        this.eventHub.$off('save-members');
        this.eventHub.$off('remove-member');
        this.eventHub.$off('save-columns');
    },

    methods: {

        setMode() {
            if(this.directoryData.mode === 'sequential'){
                this.directoryData.mode = 'directory'
            }
            else if(this.directoryData.mode === 'directory'){
                this.directoryData.mode = 'sequential'
            }
        },

        undoModeChanges() {
            this.directoryData.mode = this.kanban.mode;
            this.directoryData.messageFR = this.kanban.message_fr;
            this.directoryData.messageEN = this.kanban.message_en;
        },

        saveModeChanges() {
            this.isDraggableDisabled = true;
            this.asyncUpdateModeData(this.directoryData).then(() => {
                this.isDraggableDisabled = false;
                this.kanban.mode = this.directoryData.mode;
            });
        },

        createEmployeeCard(rowIndex, columnIndex) {
            var rowName = this.kanban.rows[rowIndex].name;
            var columnName = this.kanban.rows[rowIndex].columns[columnIndex].name;
            var columnId = this.kanban.rows[rowIndex].columns[columnIndex].id;

            this.eventHub.$emit("create-employee-cards", {
                rowIndex,
                rowName,
                columnIndex,
                columnName,
                columnId,
            });
        },
        createColumns(rowIndex, rowColumns, rowId) {
            this.eventHub.$emit("create-columns", {
                rowIndex,
                rowColumns,
                rowId,
            });
        },

        // Whenever a user drags a card
        getChangeData(event, columnIndex, rowIndex) {
            var eventName = Object.keys(event)[0];
            let employeeCardData = this.kanban.rows[rowIndex].columns[columnIndex].employee_cards
            let columnId = this.kanban.rows[rowIndex].columns[columnIndex].id
            this.isDraggableDisabled = true

            switch (eventName) {
                case "moved":
                    this.asyncUpdateEmployeeCardIndexes(employeeCardData).then(() => {
                        this.isDraggableDisabled = false
                    });
                    break;
                case "added":
                    this.asyncUpdateEmployeeCardColumnId(columnId, event.added.element.id).then(() => {
                            this.asyncUpdateEmployeeCardIndexes(employeeCardData).then(() => {
                                this.isDraggableDisabled = false
                            });
                        }
                    );
                    break;
                case "removed":
                    this.asyncUpdateEmployeeCardIndexes(employeeCardData).then(() => {
                        this.isDraggableDisabled = false
                    });
                    break;
                default:
                    alert('event "' + eventName + '" not handled: ');
            }
        },

        saveMember(selectedMembers) {
            this.loadingMembers = {memberId: null, isLoading: true}
            const cloneSelectedMembers = {...selectedMembers};
            this.asyncAddMembers(cloneSelectedMembers, this.kanban.id
            ).then(() => {

                this.asyncGetMembers(this.kanban.id).then((data) => {
                    this.kanban.members = data.data;
                    this.loadingMembers = {memberId: null, isLoading: false}
                })
            })
        },

        deleteMember(member) {
            this.asyncDeleteMember(member.id).then(() => {
                this.loadingMembers = {memberId: member.id, isLoading: true}
                this.asyncGetMembers(this.kanban.id).then((data) => {
                    this.kanban.members = data.data;
                    this.loadingMembers = {memberId: null, isLoading: false};
                })
            })

            this.getKanban(this.kanban.id);
        },

        saveEmployeeCards(cardData) {
            const cloneCardData = {...cardData};
            this.loadingCards = {columnId: cloneCardData.columnId, isLoading: true}
            this.asyncCreateEmployeeCards(cloneCardData).then(() => {
                this.asyncGetEmployeeCardsByColumn(cloneCardData.columnId).then((data) => {
                    this.kanban.rows[cloneCardData.selectedRowIndex].columns[cloneCardData.selectedColumnIndex].employee_cards = data.data;
                    this.loadingCards = {columnId: null, isLoading: false}
                })
            })
        },

        deleteEmployeeCard(cardData) {
            const cloneCardData = {...cardData};
            this.loadingCards = {columnId: cloneCardData.selectedCardData.column_id, isLoading: true}
            this.asyncDeleteEmployeeCard(cloneCardData.selectedCardData.id).then(() => {
                this.asyncGetEmployeeCardsByColumn(cloneCardData.selectedCardData.column_id).then((data) => {
                    this.kanban.rows[cloneCardData.selectedRowIndex].columns[cloneCardData.selectedColumnIndex].employee_cards = data.data;
                    this.loadingCards = {columnId: null, isLoading: false}
                })
            })
        },

        saveColumns(columnData) {
            const cloneColumnData = {...columnData};
            var rowIndex = cloneColumnData.rowIndex;
            this.loadingColumn = {rowId: this.kanban.rows[rowIndex].id, isLoading: true}

            this.asyncCreateColumns(cloneColumnData).then((data) => {
                this.kanban.rows[rowIndex].columns = data.data;
                this.loadingColumn = {rowId: null, isLoading: false}
            })
        },

        saveDirectoryData() {
            this.asyncCreateColumns(cloneColumnData).then((data) => {
                this.kanban.rows[rowIndex].columns = data.data;
                this.loadingColumn = {rowId: null, isLoading: false}
            })
        },

        getKanban(kanbanID) {
            this.eventHub.$emit("set-loading-state", true);

            this.asyncGetPhoneLineData(kanbanID).then((data) => {
                this.kanban = data.data;
                this.directoryData.kanbanId = this.kanban.id;
                this.directoryData.mode = this.kanban.mode;
                this.directoryData.messageFR = this.kanban.message_fr;
                this.directoryData.messageEN = this.kanban.message_en;
                this.eventHub.$emit("set-loading-state", false);
            })
        },
    },
};
</script>

<style scoped>
.column-width {
    min-width: 230px;
}

.ghost-card {
    opacity: 0.5;
    background: #F7FAFC;
    border: 1px solid #4299e1;
}

.toggle__dot {
    top: -0.1rem;

    transition: all 0.1s ease-in-out;
}

input:checked ~ .toggle__dot {
    transform: translateX(100%);
    background-color: #059669;
}

</style>
