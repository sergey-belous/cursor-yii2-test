export function extractApiMessage(error, fallbackMessage) {
  if (!error?.response?.data) {
    return fallbackMessage;
  }

  const { message, errors } = error.response.data;
  if (typeof message === "string" && message.length > 0) {
    return message;
  }

  if (errors && typeof errors === "object") {
    const firstField = Object.keys(errors)[0];
    if (firstField && Array.isArray(errors[firstField]) && errors[firstField].length > 0) {
      return errors[firstField][0];
    }
  }

  return fallbackMessage;
}
