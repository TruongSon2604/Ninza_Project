import { registerPlugin } from "@wordpress/plugins";
import { Card, CardBody } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { ExperimentalOrderMeta } from "@woocommerce/experimental";

const MyOrderNotesPanel = () => {
  alert("Custom Order Notes panel loaded ✅");
  return (
    <ExperimentalOrderMeta>
      <Card>
        <CardBody>
          <h3>{__("Custom Order Notes", "myshop")}</h3>
          <p>🔖 Đây là panel custom cạnh Order Notes.</p>
        </CardBody>
      </Card>
    </ExperimentalOrderMeta>
  );
};

registerPlugin("my-order-notes-plugin", { render: MyOrderNotesPanel });
