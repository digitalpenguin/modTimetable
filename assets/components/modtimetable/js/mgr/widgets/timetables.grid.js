modTimetable.grid.Timetables = function(config) {
    config = config || {};
    var me = this;
    this.actionColTpl = new Ext.XTemplate('<tpl for=".">'
        +'{control_buttons}'
            +'<tpl if="actions !== null">'
                +'<ul class="actions">'
                    +'<tpl for="actions">'
                        +'<li><button type="button" class="controlBtn {className}">{text}</button></li>'
                    +'</tpl>'
                +'</ul>'
            +'</tpl>'
        +'</tpl>', {
        compiled: true
    });
    Ext.applyIf(config,{
        id: 'modtimetable-grid-timetables'
        ,url: modTimetable.config.connectorUrl
        ,baseParams: {
            action: 'mgr/timetable/getlist'
        }
        ,save_action: 'mgr/timetable/updatefromgrid'
        ,autosave: true
        ,fields: ['id','name','description','image','active','position']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,ddGroup: 'modtimetableTimetableDDGroup'
        ,enableDragDrop: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 40
        },{
            header: _('modtimetable.timetable.name')
            ,dataIndex: 'name'
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.timetable.action')
            ,dataIndex:'control_buttons'
            ,width:250
            ,fixed:true
            ,renderer: { fn: this.actionColumnRenderer, scope: this }
        },{
            header: _('modtimetable.timetable.description')
            ,dataIndex: 'description'
            ,width: 250
            ,editor: { xtype: 'textfield' }
        },{
            header: _('modtimetable.timetable.active')
            ,dataIndex: 'active'
            ,width: 50
            ,renderer: function(value,metadata,record) {
                if(value) {
                    return '<i style="color:#4BB543;" class="icon icon-check"></i>';
                } else {
                    return '<i style="color:#FF0000;" class="icon icon-close"></i>';
                }
            }
        }/*,{
            header: _('modtimetable.timetable.image')
            ,dataIndex: 'image'
            ,width: 150
            ,fixed:true
            ,sortable:false
            ,renderer: function(value,metadata,record){
                if(!value){return;}
                if(value.charAt(0) !== '/') {
                    value = '/'+value;
                }
                return '<img style="width:100%;" src="' + value + '" />';
            }
        }*/,{
            header: _('modtimetable.timetable.position')
            ,dataIndex: 'position'
            ,width: 50
            ,hidden:true
            ,editor: { xtype: 'numberfield', allowDecimal: false, allowNegative: false }
        }]
        ,tbar: [{
            text: '<i style="margin-right:3px;" class="icon icon-plus"></i> '+_('modtimetable.timetable.create')
            ,handler: this.createTimetable
            ,scope: this
        },{
            xtype:'tbtext',
            text:'<h2 style="font-size:22px; margin-top:2px; color:#aaa;">Timetables</h2>'
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
                                    action: 'mgr/timetable/reorder'
                                    ,idItem: records.pop().id
                                    ,oldIndex: oldIndex
                                    ,newIndex: newIndex
                                }
                                ,listeners: {
                                    'success': {fn:function() { me.refresh(); },scope:this}
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
    modTimetable.grid.Timetables.superclass.constructor.call(this,config);
    this.on('click', this.onClick, this);
};
Ext.extend(modTimetable.grid.Timetables,MODx.grid.Grid,{
    windows: {}

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('modtimetable.timetable.update')
            ,handler: this.updateTimetable
        });
        m.push('-');
        m.push({
            text: _('modtimetable.timetable.remove')
            ,handler: this.removeTimetable
        });
        this.addContextMenuItem(m);
    }

    ,actionColumnRenderer: function (value, metaData, record, rowIndex, colIndex, store){
        var rec = record.data;
        var values = { days: '' };
        var h = [];
        h.push({ className:'editTimetable', text: '<i class="icon icon-edit"></i> '+_('modtimetable.timetable.edit') });
        h.push({ className:'viewDays', text: '<i class="icon icon-calendar"></i> '+_('modtimetable.timetable.view_days') });

        values.actions = h;
        return this.actionColTpl.apply(values);
    }

    ,onClick: function(e){
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if(elm === 'controlBtn'){
            var act = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            this.menu.record = record.data;
            switch (act) {
                case 'editTimetable':
                    this.updateTimetable(record, e);
                    break;
                case 'viewDays':
                    this.loadDaysCard(record, e);
                    break;
                default:
                    break;
            }
        }
    }

    ,loadDaysCard: function(record, e) {
        var cards = Ext.getCmp('timetable-card-container');
        if(Ext.getCmp('modtimetable-grid-days')){
            Ext.getCmp('modtimetable-grid-days').destroy();
        }
        var daysPanel = Ext.getCmp('modtimetable-panel-days');
        var daysGrid = MODx.load({
            xtype: 'modtimetable-grid-days',
            timetableId: record.data['id'],
            timetableRec:record,
            baseParams:{
                action: 'mgr/day/getlist',
                timetableId:record.data['id']
            }
        });
        daysPanel.add(daysGrid);
        cards.getLayout().setActiveItem(1);
        this.updateBreadcrumbs( _('modtimetable.day.managedays')+' '+record.data['name'],record);
        this.fireEvent('dayspanelloaded',record);
    }

    // Makes this card the active one
    ,activate: function() {
        var cardLayout = Ext.getCmp('timetable-card-container').getLayout();
        var oldRecord = cardLayout.activeItem.record;
        cardLayout.setActiveItem(this.id);
        this.refresh();
        this.resetBreadcrumbs('Timetables');
        cardLayout.activeItem.fireEvent('timetablesactivated',oldRecord);
    }

    // Adds a new breadcrumb to the trail
    ,updateBreadcrumbs: function(msg, rec){
        var bc = Ext.getCmp('timetable-breadcrumbs');
        var bd = bc.getData();
        bd.text = msg;

        bd.trail.shift();
        if (bd.trail.length > 0) {
            bd.trail[bd.trail.length - 1].install = true;
        }
        var newBcItem = {
            text : rec.data['name']
            ,rec: rec
        };
        bd.trail.push(newBcItem);
        bc.updateDetail(bd);
    }

    // Returns breadcrumbs back to initial state.
    ,resetBreadcrumbs: function(msg, highlight){
        msg = Ext.getCmp('timetable-breadcrumbs').desc;
        if(highlight){
            msg.text = msg;
            msg.className = 'highlight';
        }
        Ext.getCmp('timetable-breadcrumbs').reset(msg);
    }

    ,createTimetable: function(btn,e) {

        var createTimetable = MODx.load({
            xtype: 'modtimetable-window-timetable'
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        createTimetable.show(e.target);
    }

    ,updateTimetable: function(btn,e,isUpdate) {
        if (!this.menu.record || !this.menu.record.id) return false;

        var updateTimetable = MODx.load({
            xtype: 'modtimetable-window-timetable'
            ,title: _('modtimetable.timetable.update')
            ,action: 'mgr/timetable/update'
            ,record: this.menu.record
            ,listeners: {
                'success': {fn:function() { this.refresh(); },scope:this}
            }
        });

        updateTimetable.fp.getForm().reset();
        updateTimetable.fp.getForm().setValues(this.menu.record);
        updateTimetable.show(e.target);
    }
    
    ,removeTimetable: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('modtimetable.timetable.remove')
            ,text: _('modtimetable.timetable.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/timetable/remove'
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
Ext.reg('modtimetable-grid-timetables',modTimetable.grid.Timetables);

modTimetable.window.Timetable = function(config) {
    config = config || {};
    var me = this;
    Ext.applyIf(config,{
        title: _('modtimetable.timetable.create')
        ,closeAction: 'close'
        ,width:650
        ,url: modTimetable.config.connectorUrl
        ,action: 'mgr/timetable/create'
        ,fields: [{
            layout: 'column'
            , items: [{
                layout: 'form'
                , columnWidth: .5
                , items: [{
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
                    xtype: 'xcheckbox'
                    ,fieldLabel:'Active'
                    ,name: 'active'
                    ,checked: true
                    ,anchor: '100%'
                },{
                    xtype: 'textfield'
                    ,name: 'position'
                    ,hidden: true
                }]
            },{
                layout: 'form'
                ,columnWidth: .5
                ,items:[{
                    xtype: 'modx-combo-browser'
                    ,fieldLabel: 'Upload Image'
                    ,name: 'image'
                    ,id: 'location-image-field-'+Ext.id()
                    ,fixed:false
                    ,anchor:'100%'
                    ,listeners:{
                        'afterrender': function() {
                            if(config.record.id && this.getValue()) {
                                me.renderImage(this.ownerCt.getId(), this.getValue());
                            }
                        }
                        ,'select':function() {
                            me.renderImage(this.ownerCt.getId(), this.getValue());
                        }
                    }
                }]

            }]
        }]
    });
    modTimetable.window.Timetable.superclass.constructor.call(this,config);
};
Ext.extend(modTimetable.window.Timetable,MODx.Window, {
    renderImage:function(colId,path) {
        var rightCol = Ext.getCmp(colId);
        if(path.charAt(0) !== '/') {
            path = '/'+path;
        }
        rightCol.remove('school-image-preview-'+this.config.record.id);
        rightCol.add({
            html: '<img style="width:100%; margin-top:10px;" src="' + path + '" />'
            ,id: 'school-image-preview-'+this.config.record.id
        });
        rightCol.doLayout();
    }
});
Ext.reg('modtimetable-window-timetable',modTimetable.window.Timetable);

