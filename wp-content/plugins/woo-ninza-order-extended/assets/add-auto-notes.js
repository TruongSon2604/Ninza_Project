const { createElement, useState } = wp.element;


function AutoNotePanel() {
    const [note, setNote] = useState("");
    const [list, setList] = useState([]);

    const addNote = () => {
        if (!note.trim()) return;

        jQuery.post(AutoNoteData.ajaxurl, {
            action: "add_order_autonote",
            order_id: AutoNoteData.orderId,
            note: note,
            _wpnonce: AutoNoteData.nonce,
        }).done((resp) => {
            if (resp.success) {
                setList([...list, note]);
                setNote("");
            } else {
                alert("❌ Error: " + resp.data);
            }
        });
    };

    return createElement("div", { style: { padding: "5px" } },
        createElement("input", {
            type: "text",
            value: note,
            placeholder: "Enter note...",
            style: { width: "70%" },
            onChange: (e) => setNote(e.target.value),
        }),
        createElement("button", {
            type: "button",
            className: "button",
            style: { marginLeft: "5px" },
            onClick: addNote,
        }, "Add"),
        createElement("ul", { style: { marginTop: "10px" } },
            list.map((n, i) => createElement("li", { key: i }, n))
        )
    );
}

// Mount React component vào meta box
document.addEventListener("DOMContentLoaded", () => {
    const root = document.getElementById("autonote-panel-root");
    if (root) {
        wp.element.render(createElement(AutoNotePanel), root);
    } else {
        console.warn("⚠️ AutoNote root not found!");
    }
});
