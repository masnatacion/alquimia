Ext.define('Base.grid.DynamicGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.dynamicGrid',
    alternateClassName: 'Ext.grid.DynamicGrid',
    selModel: {
            mode: 'MULTI'
        },
    requires: [
        'Base.data.reader.DynamicReader'
    ],
    // URL used for request to the server. Required
    url: '',

    initComponent: function() {
        // console.log('DynamicGrid initComponent!');
        var me = this;

        if (me.url == '') {
            Ext.Error.raise('url parameter is empty! You have to set proper url to get data form server.');
        }
        else {
            Ext.applyIf(me, {
                columns: [],
                forceFit: true,
                store: Ext.create('Ext.data.Store', {
                    // Fields have to be set as empty array. Without this Ext will not create dynamic model.
                    fields: [],
                    // After loading data grid have to reconfigure columns with dynamic created columns
                    // in Ext.ux.data.reader.DynamicReader
                    listeners: {
                        'metachange': function(store, meta) {
                            
                            me.reconfigure(store, meta.columns);
                        }
                    },
                    autoLoad: true,
                    remoteSort: false,
                    remoteFilter: false,
                    remoteGroup: false,
                    proxy: {
                        reader: 'dynamicReader',
                        type: 'rest',
                        url: me.url
                    }
                })
            });
        }

        me.callParent(arguments);
    }
});