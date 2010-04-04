<?php

class CRError extends Exception{}

class YamlLoadError extends CRError{}

class InvalidConfigError extends CRError{}

class NotSupportDbError extends CRError{}

class DBError extends CRError{}

class NotSupportError extends CRError{}

class TableNotFoundError extends CRError{}

class ColumnNotFoundError extends CRError{}

?>
