<fieldset class="layui-elem-field layui-field-title">
    <legend>订单信息</legend>
</fieldset>

<table class="kg-table layui-table">
    <tr>
        <td colspan="6">订单编号：{{ order.sn }}</td>
    <tr>
    <tr>
        <td>商品信息</td>
        <td>订单金额</td>
        <td>订单类型</td>
        <td>订单状态</td>
        <td>创建时间</td>
    </tr>
    <tr>
        <td>{{ item_info(order) }}</td>
        <td>{{ '￥%0.2f'|format(order.amount) }}</td>
        <td>{{ item_type(order.item_type) }}</td>
        <td>{{ order_status(order.status) }}</td>
        <td>{{ date('Y-m-d H:i:s',order.create_time) }}</td>
    </tr>
</table>