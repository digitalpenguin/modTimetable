modTimetable.grid.Items = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'modtimetable-grid-items'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/item/getlist'
        }
        ,save_action: 'mgr/item/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','description', 'position']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,ddGroup: 'modtimetableItemDDGroup'
        ,enableDragDrop: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 70
        },{
            header: _('modtimetable.item.name')
            ,dataIndex: 'name'
            ,width: 200
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.item.description')
            ,dataIndex: 'description'
            ,width: 250
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.item.position')
            ,dataIndex: 'position'
            ,width: 250
            ,editor: { xtype: 'numberfield', allowDecimal: false, allowNegative: false }
        }]
        ,tbar: [{
            text: _('modtimetable.item.create')
            ,handler: this.createItem
            ,scope: this
        },'->',{
            xtype: 'textfield'
            ,emptyText: _('modtimetable.global.search') + '...'
            ,listeners: {
                'change': {fn:this.search,scope:this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this);
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        }]
        ,listeners: {
            'render': function(g) {
                var ddrow = new Ext.ux.dd.GridReorderDropTarget(g, {
                    copy: false
                    ,listeners: {
                        'beforerowmove': function(objThis, oldIndex, newIndex, records) {
                        }

                        ,'afterrowmove': function(objThis, oldIndex, newIndex, records) {

                            MODx.Ajax.request({
                                url: modTimetable.config.connectorUrl
                                ,params: {
                                    action: 'mgr/item/reorder'
                                    ,idItem: records.pop().id
                                    ,oldIndex: oldIndex
                                    ,newIndex: newIndex
                                }
                                ,listeners: {

                                }
                            });
                        }

                        ,'beforerowcopy': function(objThis, oldIndex, newIndex, records) {
                        }

                        ,'afterrowcopy': function(objThis, oldIndex, newIndex, records) {
                        }
                    }
                });

                Ext.dd.ScrollManager.register(g.getView().getEditorParent());
            }
            ,beforedestroy: function(g) {
                Ext.dd.ScrollManager.unregister(g.getView().getEditorParent());
            }

        }
    });
    modTimetable.grid.Items.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.grid.Items,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('modtimetable.item.update')
            ,handler: this.updateItem
        });
        m.push('-');
        m.push({
            text: _('modtimetable.item.remove')
            ,handler: this.removeItem
        });
        this.addContextMenuItem(m);
    }
    
    ,createItem: function(btn,e) {

        var createItem = MODx.load({
            xtype: 'modtimetable-window-item'
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        createItem.show(e.target);
    }

    ,updateItem: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateItem = MODx.load({
            xtype: 'modtimetable-window-item'
            ,title: _('modtimetable.item.update')
            ,action: 'mgr/item/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateItem.fp.getForm().reset();
        updateItem.fp.getForm().setValues(this.menu.record);
        updateItem.show(e.target);
    }
    
    ,removeItem: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('modtimetable.item.remove')
            ,text: _('modtimetable.item.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/item/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }

    ,search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    
    ,getDragDropText: function(){
        return this.selModel.selections.items[0].data.name;
    }
});
Ext.reg('modtimetable-grid-items',modTimetable.grid.Items);

modTimetable.window.Item = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('modtimetable.item.create')
        ,closeAction: 'close'
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/item/create'
        ,fields: [{
            xtype: 'textfield'
            ,name: 'id'
            ,hidden: true
        },{
            xtype: 'textfield'
            ,fieldLabel: _('name')
            ,name: 'name'
            ,anchor: '100%'
        },{
            xtype: 'textarea'
            ,fieldLabel: _('description')
            ,name: 'description'
            ,anchor: '100%'
        },{
            xtype: 'textfield'
            ,name: 'position'
            ,hidden: true
        }]
    });
    modTimetable.window.Item.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Item,MODx.Window);
Ext.reg('modtimetable-window-item',modTimetable.window.Item);

