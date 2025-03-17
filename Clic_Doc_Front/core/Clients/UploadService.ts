// globals.ts
export let imageURL: string | null = null;

// Function to update the global variable
export function setImageURL(url: string) {
  imageURL = url;
}

export function getImageURL(): string | null {
    return imageURL;
  }