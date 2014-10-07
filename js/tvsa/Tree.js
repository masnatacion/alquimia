Ext.define('TVSA.Tree', {
    extend      :'Ext.tree.Panel',
    title       : 'Tree 1',
    width       : 400,
    height      : 450,
    rootVisible : true,
    url         : null,
    viewConfig: {
        emptyText :"No hay resultados de b&uacute;squeda",
        stripeRows: true
    },
    node : {},
    initComponent: function (){
        
        var me = this;

        var data = {
                //fields     : this.fields,
                data     : me.data,
                //url      : me.url,
                root: {
                    expanded: false,
                    id      : 'tree',
                    text    : 'Feed'
                }
            };

        me.store = Ext.create('Ext.data.TreeStore',data);  



    me.Tree = {


        data : [],
        node : function (store,json,isRoot,idNode,newNode)
        {
            var _me = this;

            Ext.iterate(json, function(key, value) {
                // console.log(key)
                var id = idNode;

                if(Ext.isObject(value))
                    id+="."+key;
                else if(Ext.isArray(value))
                    id+="."+key+"[*]";
                else if(  Ext.isObject( key ) )
                    id+="[*]";
                else
                    id+="."+key;
                    //console.log(id)

                    //console.log(newNode)
                    // var node = store.getNodeById(idNode);
                    var expanded = isRoot;

                    if(Ext.isObject(key))
                        _me.node(store,key,false,id,newNode);

                    else{
                        var child = newNode.appendChild(Ext.apply(me.node,{
                            //expanded :expanded,

                            leaf: Ext.isObject(value) ? false : !Ext.isArray(value),
                            id   : id,
                            text : key
                        }),true);
                    }




                    if(Ext.isObject(value))
                        _me.node(store,value,false,id,child);

                    if(Ext.isArray(value)){

                        var json = value;


                        Ext.Array.each(json, function(key, value) {
                             
                            // console.log(key)

                            // console.log(key);
                            // console.log(idNode)
                            if(Ext.isObject(key))
                                _me.node(store,key,false,id,child);

               
                        });
                    }

            }); 
            

        }

    };


    me.loadData(me.url);

    me.callParent(arguments);



    me.on("beforeselect",function(_me, record, index, eOpts){
        if(!record.raw.leaf)
        return false;
    });

    // added 
    me.on("checkchange", function( node, checked, eOpts ){
        if(node.hasChildNodes()){
            node.eachChild(function(childNode){

                if(childNode.data.leaf)
                    childNode.set('checked', checked);
            });
        }

        TVSA.standar.save(TVSA.standar.hiddenInput);
    });
    


    },

    loadData : function(url){
        var me = this;
        if(!Ext.isEmpty(url))
        {
            me.setLoading("Cargando Feed");
            
            Ext.Ajax.request ({
                url: url,
                success: function (file) {
                    me.setData(file);
                }
            }); 
        }
    },
    setData : function(file)
    {
        var me = this;
        var json     = Ext.decode(file.responseText);

            var node = me.store.getRootNode();
            var Tree = me.Tree;

            if(json.length > 1)
                json = json[0];

            Tree.node(me.store,json,true,"tree",node);

            me.fireEvent("renderTree",me.store);
            node.expand();
            me.setLoading(false);
    },

    listeners : {

        'itemclick' : function(_me, record, item, index, e ){

            //panel.getComponent('tree1-panel').getComponent('tree1-label').setText(record.get("id"));
        }
    }


});