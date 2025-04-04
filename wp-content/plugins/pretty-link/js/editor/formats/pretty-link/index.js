/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { withSpokenMessages, ToolbarGroup, ToolbarButton, Icon } from "@wordpress/components";
import { Component, Fragment } from "@wordpress/element";
import {
  getTextContent,
  applyFormat,
  removeFormat,
  slice,
  registerFormatType
} from "@wordpress/rich-text";
import { isURL, isEmail } from "@wordpress/url";
import {
  RichTextShortcut,
  BlockControls
} from "@wordpress/block-editor";
import InlineLinkUI from "../../components/link-editor";

/**
 * Block constants
 */
const name = "pretty-link/pretty-link";
const title = __("Pretty Link");
const plIcon = () => (
  <Icon icon={ <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M6.844 6.809A59.24 59.24 0 0 0 3 7.371c-2.988.602-3.625.953-2.273 1.25.297.063.93.242 1.402.391l.863.277.824-.145c1.844-.316 3.328-.445 5.484-.473l2.227-.027.289-.242c.16-.137.406-.273.543-.309.223-.059 2.938.188 3.453.313.172.039.246-.031.445-.426l.242-.52c0-.062-.816-.219-2.297-.437-1.055-.16-1.863-.203-4.125-.223-1.547-.016-3.004-.012-3.234.008m1.313 2.527c-1.496.078-3.152.25-3.258.34-.062.055-.117.145-.113.203s.555.375 1.234.707l1.23.602 2.398.02 2.727.094.328.074-.582-.625c-.359-.387-.625-.766-.695-1-.102-.336-.145-.375-.41-.375a55.49 55.49 0 0 1-1.273-.035l-1.586-.004m.727 2.621c-.039.039-.07.105-.07.145s.465.43 1.035.875l1.035.805 1.129.102 1.156.07c.016-.016.18-.332.363-.699l.336-.668-.219-.203c-.145-.137-.426-.238-.816-.297-.801-.121-3.859-.223-3.949-.129" fill="rgb(10.980392%,78.823529%,95.294118%)"/><path d="M18.141 6.039c-.121.121-.547.871-.941 1.664l-.746 1.465c-.023.027-2.824-.297-3.535-.41-.289-.047-.391-.016-.586.184-.445.441-.359.602 1.105 2.102l1.348 1.379-.969 1.969-.973 2.133c0 .25.344.539.645.539.176 0 .887-.363 2.027-1.035l1.77-1.008c.02.043 1.402 1.516 2.246 2.395.793.824.996.91 1.379.602.277-.227.27-.395-.176-2.875l-.355-1.969 1.684-1 1.762-1.125c.137-.215.09-.633-.09-.812-.141-.141-.547-.219-2.039-.391l-1.906-.246c-.02-.016-.172-.801-.34-1.738-.254-1.441-.336-1.734-.512-1.879-.285-.227-.527-.211-.797.059" fill="rgb(98.823529%,72.54902%,1.960784%)"/></svg> } />
);

export const prettyLink = {
  name,
  title,
  tagName: "a",
  className: "pretty-link",
  attributes: {
    url: "href",
    target: "target"
  },
  edit: withSpokenMessages(
    class LinkEdit extends Component {
      constructor() {
        super(...arguments);

        this.addLink = this.addLink.bind(this);
        this.stopAddingLink = this.stopAddingLink.bind(this);
        this.onRemoveFormat = this.onRemoveFormat.bind(this);
        this.state = {
          addingLink: false
        };
      }

      addLink() {
        const { value, onChange } = this.props;
        const text = getTextContent(slice(value));

        if (text && isURL(text)) {
          onChange(
            applyFormat(value, { type: name, attributes: { url: text } })
          );
        } else {
          this.setState({ addingLink: true });
        }
      }

      stopAddingLink() {
        this.setState({ addingLink: false });
      }

      onRemoveFormat() {
        const { value, onChange, speak } = this.props;

        onChange(removeFormat(value, name));
        speak(__("Link removed."), "assertive");
      }

      render() {
        const { isActive, activeAttributes, value, onChange, contentRef } = this.props;

        return (
          <>
            <RichTextShortcut
              type="primary"
              character="p"
              onUse={this.addLink}
            />
            <RichTextShortcut
              type="primaryShift"
              character="p"
              onUse={this.onRemoveFormat}
            />
            {isActive && (
              <BlockControls>
                <ToolbarGroup>
                  <ToolbarButton
                    icon={plIcon}
                    title={__("Unlink")}
                    onClick={this.onRemoveFormat}
                    isActive={isActive}
                  />
                </ToolbarGroup>
              </BlockControls>
            )}
            {!isActive && (
              <BlockControls>
                <ToolbarGroup>
                  <ToolbarButton
                    icon={plIcon}
                    title={title}
                    onClick={this.addLink}
                    isActive={isActive}
                  />
                </ToolbarGroup>
              </BlockControls>
            )}
            <InlineLinkUI
              addingLink={this.state.addingLink}
              stopAddingLink={this.stopAddingLink}
              isActive={isActive}
              activeAttributes={activeAttributes}
              value={value}
              onChange={onChange}
              contentRef={contentRef}
            />
          </>
        );
      }
    }
  )
};

function registerFormats() {
  [prettyLink].forEach(({ name, ...settings }) =>
    registerFormatType(name, settings)
  );
}
registerFormats();
