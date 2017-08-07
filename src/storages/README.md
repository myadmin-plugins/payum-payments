Storages
========

Storage allow you save,fetch payment related information. They could be used explicitly, it means you have to call save or fetch methods when it is required. Or you can integrate a storage to a gateway using StorageExtension. In this case every time gateway finish to execute a request it stores the information. StorageExtension could also load a model by it is Identification so you do not have to care about that.

